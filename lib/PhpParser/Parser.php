<?php

namespace PhpParser;

interface Parser {
    /**
     * Parses PHP code into a node tree.
     *
     * @param string $code The source code to parse
     *
     * @return Node[]|null Array of statements (or null if error recovery is enabled and the parser
     *                     was unable to recover from an error).
     */
    public function parse($code);
}
