<?php
    
    /**
     * Exemplo modulo 10: 83640000001-1 33120138000-2 81288462711-6 08013618155-1
     * Exemplo modulo 11: 85890000460-9 52460179160-5 60759305086-5 83148300001-0
     */
    function validarCodigoBarras($codigoBarras) {
        
        /**
         * Verifica se é o modulo 10 ou modulo 11.
         * Se o 3º digito for 6 ou 7 é modulo 10, se for 8 ou 9, então modulo 11.
         */
        if (in_array($codigoBarras[2], [6, 7])) {
            $isModulo10 = true;
            $modulo     = 10;
        } else {
            $isModulo10 = false;
            $modulo     = 11;
        }

        $valido = 0;

        foreach (explode(' ', $codigoBarras) as $bloco) {
            list($codigo, $digitoVerificador) = explode('-', $bloco);

            $codigo = strrev($codigo);
            $codigo = str_split($codigo);

            $somatorio = 0;

            foreach ($codigo as $index => $value) {
                if ($isModulo10) {
                    $soma = $value * ($index % 2 == 0 ? 2 : 1);
                    
                    /**
                     * Quando a $soma tiver mais de 1 algarismo(ou seja, maior que 9), 
                     * soma-se os algarismos antes de aomar com $somatorio
                     */
                    if ($soma > 9) {
                        $somatorio += array_sum(str_split($soma));
                    } else {
                        $somatorio += $soma;
                    }
                } else {
                    $somatorio += $value * (2 + ($index >= 8 ? $index - 8 : $index));
                }
            }

            $restoDivisao= $somatorio % $modulo;
            
            if (!$isModulo10 && ($restoDivisao == 0 || $restoDivisao == 1)) {
                $moduloMenosRestoDivisao = 0;
            } else if (!$isModulo10 && $restoDivisao == 10) {
                $moduloMenosRestoDivisao = 1;
            } else {
                $moduloMenosRestoDivisao = $modulo - $restoDivisao;
            }
            
            if ($moduloMenosRestoDivisao == $digitoVerificador) {
                $valido++;
            }
        }
    
        return $valido == 4;
    }