<?php

namespace App\Helpers;

use App\Log;
use Exception;
use Illuminate\Support\Facades\Log as FacadesLog;
use stdClass;

class Logger implements LoggerBuilder
{
    private $log;

    private function fillDefaultProperties()
    {
        if (!isset($this->log->description)) {
            switch ($this->log->type) {
                case 'success':
                    $this->log->description = 'Ok al ' . $this->log->action . ' registro';
                    break;
                case 'error':
                    $this->log->description = 'Error al ' . $this->log->action . ' registro';
                    break;
            }
        }
        $this->log->link_id = $this->log->link_id ?? -1;
        $this->log->module = ($this->log->module ?? 'UNDEFINED') . (isset($this->log->method) ? '::' . $this->log->method : '');
        $this->log->exception = $this->log->exception ?? null;
        $this->log->before = $this->log->before ?? null;
        $this->log->after = $this->log->after ?? null;
        $this->log->user_id = $this->log->user_id ?? 0;
    }

    public function log()
    {
        try {
            $this->fillDefaultProperties();

            Log::create([
                "link_id" => $this->log->link_id,
                "modulo" => $this->log->module,
                "tipo" => $this->log->type,
                "descripcion" => $this->log->description,
                "excepcion" => $this->log->exception == null ? "NA" : $this->log->exception->getMessage() . ' ' . $this->log->exception->getTraceAsString(),
                "antes" => $this->log->before,
                "despues" => $this->log->after,
                "usuario_id" => $this->log->user_id
            ]);

            $this->reinitialize();
        } catch (Exception $e) {
            FacadesLog::error("Error al guardar en el log: 
                               ex:          " . $e->getMessage() . " " . $e->getTraceAsString());
        }
    }

    public function success($action = ''): LoggerBuilder
    {
        $this->issetLogObject();

        $this->log->type = 'success';
        $this->log->action = $action;

        return $this;
    }

    public function info($action = ''): LoggerBuilder
    {
        $this->issetLogObject();

        $this->log->type = 'info';
        $this->log->action = $action;

        return $this;
    }

    public function error($action = ''): LoggerBuilder
    {
        $this->issetLogObject();

        $this->log->type = 'error';
        $this->log->action = $action;

        return $this;
    }

    public function module($value): LoggerBuilder
    {
        $this->issetLogObject();

        $this->log->module = $value;

        return $this;
    }

    public function method($value): LoggerBuilder
    {
        $this->issetLogObject();

        $this->log->method = $value;

        return $this;
    }

    public function exception($value): LoggerBuilder
    {
        $this->issetLogObject();
        $this->log->exception = $value;
        return $this;
    }

    public function before($value): LoggerBuilder
    {
        $this->issetLogObject();
        $this->log->before = $value;
        return $this;
    }

    public function after($value): LoggerBuilder
    {
        $this->issetLogObject();
        $this->log->after = $value;
        return $this;
    }

    public function description($value): LoggerBuilder
    {
        $this->issetLogObject();
        $this->log->description = $value;
        return $this;
    }
    public function link_id($value): LoggerBuilder
    {
        $this->issetLogObject();
        $this->log->link_id = $value;
        return $this;
    }

    public function user_id($value): LoggerBuilder
    {
        $this->issetLogObject();
        $this->log->user_id = $value;
        return $this;
    }

    private function issetLogObject()
    {
        if (!isset($this->log))
            $this->reinitialize();
    }

    private function reinitialize()
    {
        $this->log = new stdClass;
    }
}
