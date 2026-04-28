<?php


use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::unprepared('CREATE TRIGGER after_pagos_update
            AFTER UPDATE ON pagos
            FOR EACH ROW
            BEGIN
                -- Actualizar saldo
                UPDATE tarotistas t
                SET t.saldo = (
                    -- Total de llamadas completadas
                    COALESCE((
                        SELECT SUM(l.total)
                        FROM llamadas l
                        INNER JOIN cliente_tarotista ct ON ct.id = l.fk_cliente_tarotista
                        WHERE ct.fk_tarotista = NEW.fk_tarotista
                        AND l.estado_llamada = 4
                    ), 0)
                    -
                    -- Total de pagos realizados
                    COALESCE((
                        SELECT SUM(p.valor)
                        FROM pagos p
                        WHERE p.fk_tarotista = NEW.fk_tarotista
                    ), 0)
                )
                WHERE t.id = NEW.fk_tarotista;

            END');

        DB::unprepared('CREATE TRIGGER after_pagos_insert
            AFTER INSERT ON pagos
            FOR EACH ROW
            BEGIN
                -- Actualizar saldo
                UPDATE tarotistas t
                SET t.saldo = (
                    -- Total de llamadas completadas
                    COALESCE((
                        SELECT SUM(l.total)
                        FROM llamadas l
                        INNER JOIN cliente_tarotista ct ON ct.id = l.fk_cliente_tarotista
                        WHERE ct.fk_tarotista = NEW.fk_tarotista
                        AND l.estado_llamada = 4
                    ), 0)
                    -
                    -- Total de pagos realizados
                    COALESCE((
                        SELECT SUM(p.valor)
                        FROM pagos p
                        WHERE p.fk_tarotista = NEW.fk_tarotista
                    ), 0)
                )
                WHERE t.id = NEW.fk_tarotista;

            END');

        DB::unprepared('CREATE TRIGGER after_llamadas_update
            AFTER UPDATE ON llamadas
            FOR EACH ROW
            BEGIN
                DECLARE v_tarotista_id INT;

                -- Obtener el tarotista asociado a la llamada
                SELECT fk_tarotista
                INTO v_tarotista_id
                FROM cliente_tarotista
                WHERE id = NEW.fk_cliente_tarotista;

                -- Actualizar saldo
                UPDATE tarotistas t
                SET t.saldo = (
                    -- Total de llamadas completadas
                    COALESCE((
                        SELECT SUM(l.total)
                        FROM llamadas l
                        INNER JOIN cliente_tarotista ct ON ct.id = l.fk_cliente_tarotista
                        WHERE ct.fk_tarotista = v_tarotista_id
                        AND l.estado_llamada = 4
                    ), 0)
                    -
                    -- Total de pagos realizados
                    COALESCE((
                        SELECT SUM(p.valor)
                        FROM pagos p
                        WHERE p.fk_tarotista = v_tarotista_id
                    ), 0)
                )
                WHERE t.id = v_tarotista_id;

            END');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::unprepared('DROP TRIGGER IF EXISTS after_pagos_update');
        DB::unprepared('DROP TRIGGER IF EXISTS after_llamadas_update');
        DB::unprepared('DROP TRIGGER IF EXISTS after_pagos_insert');
        
    }
};
