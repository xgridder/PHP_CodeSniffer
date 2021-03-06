<?php
/**
 * Checks that all PHP types are lowercase.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Standards\Generic\Sniffs\PHP;

use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Util\Tokens;

class LowerCaseTypeSniff implements Sniff
{


    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register()
    {
        $tokens   = Tokens::$castTokens;
        $tokens[] = T_FUNCTION;
        $tokens[] = T_CLOSURE;
        return $tokens;

    }//end register()


    /**
     * Processes this sniff, when one of its tokens is encountered.
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The file being scanned.
     * @param int                         $stackPtr  The position of the current token in the
     *                                               stack passed in $tokens.
     *
     * @return void
     */
    public function process(File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        if (isset(Tokens::$castTokens[$tokens[$stackPtr]['code']]) === true) {
            // A cast token.
            if (strtolower($tokens[$stackPtr]['content']) !== $tokens[$stackPtr]['content']) {
                if ($tokens[$stackPtr]['content'] === strtoupper($tokens[$stackPtr]['content'])) {
                    $phpcsFile->recordMetric($stackPtr, 'PHP type case', 'upper');
                } else {
                    $phpcsFile->recordMetric($stackPtr, 'PHP type case', 'mixed');
                }

                $error = 'PHP types must be lowercase; expected "%s" but found "%s"';
                $data  = [
                    strtolower($tokens[$stackPtr]['content']),
                    $tokens[$stackPtr]['content'],
                ];

                $fix = $phpcsFile->addFixableError($error, $stackPtr, 'Found', $data);
                if ($fix === true) {
                    $phpcsFile->fixer->replaceToken($stackPtr, strtolower($tokens[$stackPtr]['content']));
                }
            } else {
                $phpcsFile->recordMetric($stackPtr, 'PHP type case', 'lower');
            }//end if

            return;
        }//end if

        $phpTypes = [
            'self'     => true,
            'array'    => true,
            'callable' => true,
            'bool'     => true,
            'float'    => true,
            'int'      => true,
            'string'   => true,
            'iterable' => true,
        ];

        $props      = $phpcsFile->getMethodProperties($stackPtr);
        $returnType = $props['return_type'];
        if ($returnType !== ''
            && isset($phpTypes[strtolower($returnType)]) === true
        ) {
            // A function return type.
            if (strtolower($returnType) !== $returnType) {
                if ($returnType === strtoupper($returnType)) {
                    $phpcsFile->recordMetric($stackPtr, 'PHP type case', 'upper');
                } else {
                    $phpcsFile->recordMetric($stackPtr, 'PHP type case', 'mixed');
                }

                $error = 'PHP types must be lowercase; expected "%s" but found "%s"';
                $data  = [
                    strtolower($returnType),
                    $returnType,
                ];

                $fix = $phpcsFile->addFixableError($error, $stackPtr, 'Found', $data);
                if ($fix === true) {
                    $token = $props['return_type_token'];
                    $phpcsFile->fixer->replaceToken($token, strtolower($tokens[$token]['content']));
                }
            } else {
                $phpcsFile->recordMetric($stackPtr, 'PHP type case', 'lower');
            }//end if
        }//end if

        $params = $phpcsFile->getMethodParameters($stackPtr);
        foreach ($params as $param) {
            $typeHint = $param['type_hint'];
            if ($typeHint !== ''
                && isset($phpTypes[strtolower($typeHint)]) === true
            ) {
                // A function return type.
                if (strtolower($typeHint) !== $typeHint) {
                    if ($typeHint === strtoupper($typeHint)) {
                        $phpcsFile->recordMetric($stackPtr, 'PHP type case', 'upper');
                    } else {
                        $phpcsFile->recordMetric($stackPtr, 'PHP type case', 'mixed');
                    }

                    $error = 'PHP types must be lowercase; expected "%s" but found "%s"';
                    $data  = [
                        strtolower($typeHint),
                        $typeHint,
                    ];

                    $fix = $phpcsFile->addFixableError($error, $stackPtr, 'Found', $data);
                    if ($fix === true) {
                        $token = $param['type_hint_token'];
                        $phpcsFile->fixer->replaceToken($token, strtolower($tokens[$token]['content']));
                    }
                } else {
                    $phpcsFile->recordMetric($stackPtr, 'PHP type case', 'lower');
                }//end if
            }//end if
        }//end foreach

    }//end process()


}//end class
