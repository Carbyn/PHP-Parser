<?php

namespace PhpParser;

use PhpParser\Comment;

require_once __DIR__ . '/CodeTestAbstract.php';

class CodeParsingTest extends CodeTestAbstract
{
    /**
     * @dataProvider provideTestParse
     */
    public function testParse($name, $code, $expected, $mode) {
        $lexer = new Lexer\Emulative(array('usedAttributes' => array(
            'startLine', 'endLine', 'startFilePos', 'endFilePos', 'comments'
        )));
        $errors5 = new ErrorHandler\Collecting();
        $errors7 = new ErrorHandler\Collecting();
        $parser5 = new Parser\Php5($lexer, array(
            'errorHandler' => $errors5,
        ));
        $parser7 = new Parser\Php7($lexer, array(
            'errorHandler' => $errors7,
        ));

        $output5 = $this->getParseOutput($parser5, $errors5, $code);
        $output7 = $this->getParseOutput($parser7, $errors7, $code);

        if ($mode === 'php5') {
            $this->assertSame($expected, $output5, $name);
            $this->assertNotSame($expected, $output7, $name);
        } else if ($mode === 'php7') {
            $this->assertNotSame($expected, $output5, $name);
            $this->assertSame($expected, $output7, $name);
        } else {
            $this->assertSame($expected, $output5, $name);
            $this->assertSame($expected, $output7, $name);
        }
    }

    private function getParseOutput(Parser $parser, ErrorHandler\Collecting $errors, $code) {
        $stmts = $parser->parse($code);

        $output = '';
        foreach ($errors->getErrors() as $error) {
            $output .= $this->formatErrorMessage($error, $code) . "\n";
        }

        if (null !== $stmts) {
            $dumper = new NodeDumper(['dumpComments' => true]);
            $output .= $dumper->dump($stmts);
        }

        return canonicalize($output);
    }

    public function provideTestParse() {
        return $this->getTests(__DIR__ . '/../code/parser', 'test');
    }

    private function formatErrorMessage(Error $e, $code) {
        if ($e->hasColumnInfo()) {
            return $e->getMessageWithColumnInfo($code);
        } else {
            return $e->getMessage();
        }
    }
}
