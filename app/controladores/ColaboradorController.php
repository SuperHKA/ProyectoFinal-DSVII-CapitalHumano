<?php

declare(strict_types=1);

namespace App\Controladores;

use App\Modelos\Catalogo;
use App\Modelos\Colaborador;
use App\Modelos\HistorialAcademico;
use App\Servicios\AuthService;
use App\Servicios\FileUploadService;
use App\Utilidades\FormState;
use App\Utilidades\Sanitizer;
use App\Utilidades\Validator;
use Throwable;

/**
 * Controla colaboradores, perfiles laborales, promociones, bajas e historial academico.
 */
final class ColaboradorController
{
    private Colaborador $colaboradores;
    private Catalogo $catalogos;
    private HistorialAcademico $historial;
    private FileUploadService $uploads;

    public function __construct()
    {
        $this->colaboradores = new Colaborador();
        $this->catalogos = new Catalogo();
        $this->historial = new HistorialAcademico();
        $this->uploads = new FileUploadService();
    }

    public function index(): void
    {
        AuthService::ensure('colaboradores', 'ver');
        $q = Sanitizer::text($_GET['q'] ?? '');
        $page = Sanitizer::int($_GET['page'] ?? 1);
        $page = $page < 1 ? 1 : $page;
        $paginacion = $this->colaboradores->paginated($q, $page, 15);
        render_view('colaboradores/index', [
            'title' => 'Colaboradores',
            'colaboradores' => $paginacion['items'],
            'paginacion' => $paginacion,
            'q' => $q,
        ]);
    }

    public function create(): void
    {
        AuthService::ensure('colaboradores', 'crear');
        render_view('colaboradores/create', [
            'title' => 'Nuevo colaborador',
            'catalogos' => $this->catalogos->formCatalogs(),
            'old' => FormState::old(),
            'errors' => FormState::errors(),
        ]);
    }

    public function store(): void
    {
        AuthService::ensure('colaboradores', 'crear');
        try {
            require_secure_post();
            $data = Sanitizer::colaborador($_POST);
            $errors = Validator::colaborador($data, $this->catalogos->formCatalogs());
            redirect_if_invalid($data, $errors, 'colaboradores.create');

            $data['fotografia'] = $this->uploads->uploadImage($_FILES['fotografia'] ?? [], 'fotos');
            $id = $this->colaboradores->create($data);
            flash('success', 'Colaborador registrado correctamente.');
            redirect_to('colaboradores.show', ['id' => $id]);
        } catch (Throwable $exception) {
            FormState::flash($_POST, [safe_error_message($exception, 'No fue posible registrar el colaborador.', ['module' => 'colaboradores', 'action' => 'create'])]);
            redirect_to('colaboradores.create');
        }
    }

    public function edit(): void
    {
        AuthService::ensure('colaboradores', 'editar');
        $id = Sanitizer::int($_GET['id'] ?? 0);
        $colaborador = $this->findOrRedirect($id);

        render_view('colaboradores/edit', [
            'title' => 'Editar colaborador',
            'colaborador' => $colaborador,
            'catalogos' => $this->catalogos->formCatalogs(),
            'old' => FormState::old(),
            'errors' => FormState::errors(),
        ]);
    }

    public function update(): void
    {
        AuthService::ensure('colaboradores', 'editar');
        $id = Sanitizer::int($_GET['id'] ?? 0);
        try {
            require_secure_post();
            $data = Sanitizer::colaborador($_POST);
            $errors = Validator::colaborador($data, $this->catalogos->formCatalogs());
            redirect_if_invalid($data, $errors, 'colaboradores.edit', ['id' => $id]);

            $foto = $this->uploads->uploadImage($_FILES['fotografia'] ?? [], 'fotos');
            if ($foto !== null) {
                $data['fotografia'] = $foto;
            }
            $this->colaboradores->update($id, $data);
            flash('success', 'Colaborador actualizado correctamente.');
            redirect_to('colaboradores.show', ['id' => $id]);
        } catch (Throwable $exception) {
            FormState::flash($_POST, [safe_error_message($exception, 'No fue posible actualizar el colaborador.', ['module' => 'colaboradores', 'action' => 'update'])]);
            redirect_to('colaboradores.edit', ['id' => $id]);
        }
    }

    public function show(): void
    {
        AuthService::ensure('colaboradores', 'ver');
        $id = Sanitizer::int($_GET['id'] ?? 0);
        $colaborador = $this->findOrRedirect($id);

        render_view('colaboradores/show', [
            'title' => 'Detalle del colaborador',
            'colaborador' => $colaborador,
            'historial' => $this->historial->porColaborador($id),
        ]);
    }

    public function promoteForm(): void
    {
        AuthService::ensure('colaboradores', 'promover');
        $id = Sanitizer::int($_GET['id'] ?? 0);
        $colaborador = $this->findOrRedirect($id);

        render_view('colaboradores/promote', [
            'title' => 'Registrar promocion',
            'colaborador' => $colaborador,
            'catalogos' => $this->catalogos->formCatalogs(),
            'old' => FormState::old(),
            'errors' => FormState::errors(),
        ]);
    }

    public function promote(): void
    {
        AuthService::ensure('colaboradores', 'promover');
        $id = Sanitizer::int($_GET['id'] ?? 0);
        try {
            require_secure_post();
            $data = Sanitizer::perfilLaboral($_POST);
            $errors = Validator::perfilLaboral($data, $this->catalogos->formCatalogs());
            redirect_if_invalid($data, $errors, 'colaboradores.promoteForm', ['id' => $id]);

            $this->colaboradores->promote($id, $data);
            flash('success', 'Promocion registrada y cargo anterior desactivado.');
            redirect_to('colaboradores.show', ['id' => $id]);
        } catch (Throwable $exception) {
            FormState::flash($_POST, [safe_error_message($exception, 'No fue posible registrar la promocion.', ['module' => 'colaboradores', 'action' => 'promote'])]);
            redirect_to('colaboradores.promoteForm', ['id' => $id]);
        }
    }

    public function bajaForm(): void
    {
        AuthService::ensure('colaboradores', 'baja');
        $id = Sanitizer::int($_GET['id'] ?? 0);
        $colaborador = $this->findOrRedirect($id);

        render_view('colaboradores/baja', [
            'title' => 'Registrar baja',
            'colaborador' => $colaborador,
            'catalogos' => $this->catalogos->formCatalogs(),
            'old' => FormState::old(),
            'errors' => FormState::errors(),
        ]);
    }

    public function baja(): void
    {
        AuthService::ensure('colaboradores', 'baja');
        $id = Sanitizer::int($_GET['id'] ?? 0);
        try {
            require_secure_post();
            $data = Sanitizer::baja($_POST);
            $errors = Validator::baja($data, $this->catalogos->formCatalogs());
            redirect_if_invalid($data, $errors, 'colaboradores.bajaForm', ['id' => $id]);

            $this->colaboradores->baja($id, $data);
            flash('success', 'Baja registrada correctamente.');
            redirect_to('colaboradores.show', ['id' => $id]);
        } catch (Throwable $exception) {
            FormState::flash($_POST, [safe_error_message($exception, 'No fue posible registrar la baja.', ['module' => 'colaboradores', 'action' => 'termination'])]);
            redirect_to('colaboradores.bajaForm', ['id' => $id]);
        }
    }

    public function reincorporateForm(): void
    {
        AuthService::ensure('colaboradores', 'reincorporar');
        $id = Sanitizer::int($_GET['id'] ?? 0);
        $colaborador = $this->findOrRedirect($id);
        if ((int) $colaborador['empleado_activo'] === 1) {
            flash('error', 'El colaborador ya se encuentra activo.');
            redirect_to('colaboradores.show', ['id' => $id]);
        }

        render_view('colaboradores/reincorporate', [
            'title' => 'Reincorporar colaborador',
            'colaborador' => $colaborador,
            'catalogos' => $this->catalogos->formCatalogs(),
            'old' => FormState::old(),
            'errors' => FormState::errors(),
        ]);
    }

    public function reincorporate(): void
    {
        AuthService::ensure('colaboradores', 'reincorporar');
        $id = Sanitizer::int($_GET['id'] ?? 0);
        try {
            require_secure_post();
            $data = Sanitizer::perfilLaboral($_POST);
            $errors = Validator::perfilLaboral($data, $this->catalogos->formCatalogs());
            redirect_if_invalid($data, $errors, 'colaboradores.reincorporateForm', ['id' => $id]);

            $this->colaboradores->reincorporate($id, $data);
            flash('success', 'Colaborador reincorporado con un nuevo periodo laboral.');
            redirect_to('colaboradores.show', ['id' => $id]);
        } catch (Throwable $exception) {
            $knownMessages = [
                'El colaborador solicitado no existe.',
                'El colaborador ya se encuentra activo.',
                'La reincorporacion debe iniciar despues de la ultima fecha de baja.',
            ];
            $message = in_array($exception->getMessage(), $knownMessages, true)
                ? $exception->getMessage()
                : safe_error_message($exception, 'No fue posible reincorporar al colaborador.', ['module' => 'colaboradores', 'action' => 'reincorporate']);
            FormState::flash($_POST, [$message]);
            redirect_to('colaboradores.reincorporateForm', ['id' => $id]);
        }
    }

    public function destroy(): void
    {
        AuthService::ensure('colaboradores', 'eliminar');
        try {
            require_secure_post();
            $this->colaboradores->delete(Sanitizer::int($_GET['id'] ?? 0));
            flash('success', 'Colaborador eliminado correctamente.');
        } catch (Throwable $exception) {
            flash('error', safe_error_message($exception, 'No fue posible eliminar el colaborador.', ['module' => 'colaboradores', 'action' => 'delete']));
        }

        redirect_to('colaboradores.index');
    }

    public function addHistorial(): void
    {
        AuthService::ensure('colaboradores', 'editar');
        try {
            require_secure_post();
            $data = [
                'colaborador_id' => Sanitizer::int($_GET['id'] ?? 0),
                'titulo' => Sanitizer::text($_POST['titulo'] ?? ''),
                'institucion' => Sanitizer::text($_POST['institucion'] ?? ''),
            ];
            if ($data['titulo'] === '' || $data['institucion'] === '') {
                flash('error', 'Titulo e institucion son obligatorios.');
                redirect_to('colaboradores.show', ['id' => $data['colaborador_id']]);
            }

            $data['archivo_pdf'] = $this->uploads->uploadPdf($_FILES['archivo_pdf'] ?? []);
            $this->historial->create($data);
            flash('success', 'Historial academico agregado.');
        } catch (Throwable $exception) {
            flash('error', safe_error_message($exception, 'No fue posible guardar el historial academico.', ['module' => 'historial', 'action' => 'create']));
        }
        redirect_to('colaboradores.show', ['id' => Sanitizer::int($_GET['id'] ?? 0)]);
    }

    public function removeHistorial(): void
    {
        AuthService::ensure('colaboradores', 'editar');
        try {
            require_secure_post();
            $id = Sanitizer::int($_GET['id'] ?? 0);
            $this->historial->delete($id);
            flash('success', 'Registro eliminado.');
        } catch (Throwable $exception) {
            flash('error', safe_error_message($exception, 'No fue posible eliminar el registro academico.', ['module' => 'historial', 'action' => 'delete']));
        }
        redirect_to('colaboradores.show', ['id' => Sanitizer::int($_GET['colaborador_id'] ?? 0)]);
    }

    /**
     * @return array<string,mixed>
     */
    private function findOrRedirect(int $id): array
    {
        $colaborador = $this->colaboradores->find($id);
        if ($colaborador === null) {
            flash('error', 'El colaborador solicitado no existe.');
            redirect_to('colaboradores.index');
        }

        return $colaborador;
    }
}
