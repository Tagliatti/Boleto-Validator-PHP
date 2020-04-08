<?php

namespace Tagliatti\BoletoValidator;

/**
 * Classe para validação de código de barras e
 * linha digitalizável presente em boletos bancários.
 *
 * @author Filipe Tagliatti <filipetagliatti@gmail.com>
 */
class BoletoValidator
{

    /**
     * Valida boletos do tipo convênio.
     *
     * @example Exemplo modulo 10: 83640000001-1 33120138000-2 81288462711-6 08013618155-1
     * @example Exemplo modulo 11: 85890000460-9 52460179160-5 60759305086-5 83148300001-0
     *
     * @param string $codigoBarras Código de barras com ou sem mascara.
     * @throws Exception Caso o formato do boleto não atender as especificações.
     * @return boolean Retorna se é válido ou não.
     */
    public static function convenio($codigoBarras)
    {
        $codigoBarras = str_replace([' ', '-'], '', $codigoBarras);

        if (!preg_match("/^[0-9]{48}$/", $codigoBarras)) {
            throw new \Exception("Envalid format.");
        }

        $blocos[0] = substr($codigoBarras, 0, 12);
        $blocos[1] = substr($codigoBarras, 12, 12);
        $blocos[2] = substr($codigoBarras, 24, 12);
        $blocos[3] = substr($codigoBarras, 36, 12);

        /**
         * Verifica se é o modulo 10 ou modulo 11.
         * Se o 3º digito for 6 ou 7 é modulo 10, se for 8 ou 9, então modulo 11.
         */
        $isModulo10 = in_array($codigoBarras[2], [6, 7]);

        $valido = 0;

        foreach ($blocos as $bloco) {
            if ($isModulo10 && static::modulo10($bloco)) {
                $valido++;
            } elseif (static::modulo11($bloco)) {
                $valido++;
            }
        }

        return $valido == 4;
    }

    /**
     * Valida boletos do tipo fatura ou carnê.
     *
     * Neste caso, adicionei mais uma regra, que implica em boletos que não
     * possuem validade ou valor, como alguns boletos de cartão de crédido
     *
     * @example Exemplo: 42297.11504 00001.954411 60020.034520 2 68610000054659
     *                   42297.11504 00001.954411 60020.034520 2 000
     *
     * @param string $linhaDigitavel Linha digitalizável com ou sem mascara.
     * @throws Exception Caso o formato do boleto não atender as especificações.
     * @return boolean Retorna se é válido ou não.
     */
    public static function boleto($linhaDigitavel)
    {
        $linhaDigitavel = str_replace([' ', '.'], '', $linhaDigitavel);

        if ( ! preg_match("/^(?=[0-9]*$)((?:.{36}|.{47})|(?:.{36}.000$))$/", $linhaDigitavel) ) {
            throw new \Exception("Envalid format.");
        }

        $blocos[0] = substr($linhaDigitavel, 0, 10);
        $blocos[1] = substr($linhaDigitavel, 10, 11);
        $blocos[2] = substr($linhaDigitavel, 21, 11);

        $valido = 0;

        foreach ($blocos as $bloco) {
            if (static::modulo10($bloco)) {
                $valido++;
            }
        }

        return $valido == 3;
    }

    /**
     * Cacula o módulo 10 do bloco.
     *
     * @param string $bloco
     * @return boolean Retorna se é válido ou não.
     */
    protected static function modulo10($bloco)
    {
        $tamanhoBloco = strlen($bloco) - 1;
        $digitoVerificador = $bloco[$tamanhoBloco];

        $codigo = substr($bloco, 0, $tamanhoBloco);
        $codigo = strrev($codigo);
        $codigo = str_split($codigo);

        $somatorio = 0;

        foreach ($codigo as $index => $value) {
            $soma = $value * ($index % 2 == 0 ? 2 : 1);

            /**
             * Quando a $soma tiver mais de 1 algarismo(ou seja, maior que 9),
             * soma-se os algarismos antes de somar com $somatorio
             */
            if ($soma > 9) {
                $somatorio += array_sum(str_split($soma));
            } else {
                $somatorio += $soma;
            }
        }

        /**
         * (ceil($somatorio / 10) * 10) pega a dezena imediatamente superior ao $somatorio
         * (dezena superior de 25 é 30, a de 43 é 50...).
         */
        $dezenaSuperiorSomatorioMenosSomatorio = (ceil($somatorio / 10) * 10) - $somatorio;

        return $dezenaSuperiorSomatorioMenosSomatorio == $digitoVerificador;
    }

    /**
     * Cacula o módulo 11 do bloco.
     *
     * @param string $bloco
     * @return boolean Retorna se é válido ou não.
     */
    protected static function modulo11($bloco)
    {
        $tamanhoBloco = strlen($bloco) - 1;
        $digitoVerificador = $bloco[$tamanhoBloco];

        $codigo = substr($bloco, 0, $tamanhoBloco);
        $codigo = strrev($codigo);
        $codigo = str_split($codigo);

        $somatorio = 0;

        foreach ($codigo as $index => $value) {
            $somatorio += $value * (2 + ($index >= 8 ? $index - 8 : $index));
        }

        $restoDivisao = $somatorio % 11;

        if ($restoDivisao == 0 || $restoDivisao == 1) {
            $dezenaSuperiorSomatorioMenosSomatorio = 0;
        } elseif ($restoDivisao == 10) {
            $dezenaSuperiorSomatorioMenosSomatorio = 1;
        } else {
            $dezenaSuperiorSomatorioMenosSomatorio = (ceil($somatorio / 11) * 11) - $somatorio;
        }

        return $dezenaSuperiorSomatorioMenosSomatorio == $digitoVerificador;
    }
}
