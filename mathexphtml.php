<?php

/**
 * mathExpHtml Class
 *
 * This class is free for the educational use as long as maintain this header together with this class.
 * Author: Win Aung Cho
 * Contact winaungcho@gmail.com
 * version 1.1 26-11-2022
 * version 1.2 28-11-2022
 * version 1.3
 * Date: 1-12-2022
 */
class mathExpHtml
{
    private $tokens;
    public function __construct($str)
    {
        define('_OPERATORS', array(
            '(',
            ')',
            '+',
            '-',
            '*',
            '/',
            '^',
            '=',
            '<',
            '>',
            ',',
            '!',
            '[',
            '±',
            '×'
            
        ));
        $this->tokens = $this->str2tokens($str);
    }

    private function operators()
    {
        return _OPERATORS;
    }

    private function str2tokens($str)
    {
        $tokens = preg_split('/([<>()*\/=^+-])\s*|([\d.]+)\s*|([a-zA-Z0-9αβγδεζηθλμνξπρστφψω]+)\s*|({\w+})\s*/u', $str, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);
        return $tokens;
    }
    private function tokentype($token)
    {
        return (is_numeric($token) ? "value" : (in_array($token, _OPERATORS) ? "operator" : (preg_match('/^[a-zA-Z0-9\p{Greek}]+$/u', $token) ? "alphanum" : 
        	(preg_match('/({\w+})/', $token)?"vector":"none"))));
    }
    private function vechtml($b){
    	if (is_array($b)){
    		if (count($b) == 1) return $b[0];
        	$bhtml = "<table class=\"matrix\">";
        	foreach ($b as $r){
        		$bhtml .= "<tr>";
        		if (is_array($r)){
        			foreach ($r as $c){
        				$bhtml .= "<td>".$c."</td>";
        			}
        		} else $bhtml .= "<td>".$r."</td>";
        		$bhtml .= "</tr>";
        	}
        	$bhtml .= "</table>";
        	return $bhtml;
        } else return $b;
    }
    private function mathhtml($a, $o, $b)
    {
        $html = "";
        $a = $this->vechtml($a);
        $b = $this->vechtml($b);
        switch ($o)
        {
            case '+':
            case '-':
            case '=':
            case '<':
            case '>':
            case ',':
            case '×':
            case '±':
                $html = "$a $o $b";
                break;
            case '*':
                $html = "$a • $b";
                break;
            case '^':
                $html = "$a<sup>$b</sup>";
                break;
            case '[':
                $html = "$a<sub>$b</sub>";
                break;
            case '/':
                $html = "<div class=\"fraction\">";
                $html .= "<span class=\"numer\">$a</span>"; // numerator
                $html .= "<span class=\"bar\">/</span>";
                $html .= "<span class=\"denom\">$b</span>"; // denominator
                $html .= "</div>";
                break;
        }
        return ($html);
    }

    private function testknownoperator($name, $token)
    {
    	$html = "";
    	
    	if (strip_tags($name) == 'sum'){
    		$html = "<span class=\"intsuma\">".
    		"<span class=\"lim\">".$token[1]."</span>".
    		"<span class=\"sum-frac\">&sum;</span>".
    		"<span class=\"lim\">".$token[0]."</span>".
    		"</span>".$token[2];
    	} else if (strip_tags($name) == 'int'){
    		$html = "<span class=\"intsuma\">".
    		"<span class=\"lim\">".$token[1]."</span>".
    		"<span class=\"sum-frac\">&int;</span>".
    		"<span class=\"lim\">".$token[0]."</span>".
    		"</span>".$token[2]. $token[3];
    	} else {
    		$l = count($token);
    		$html = $name."(";
    		for ($i=0;$i<$l;$i++) {
    			if ($i > 0)
    				$html .= ',';
    			$html .= $token[$i];
    		}
    		$html .= ")";
    	}
    	return $html;
    }

    private function formatelement($tokens)
    {
        $len = count($tokens);
        for ($i = 0;$i < $len;$i++) {
            $a = is_array($tokens[$i])? $tokens[$i]:trim($tokens[$i]);
            $b = ($i < $len - 1) ? trim($tokens[$i + 1]) : " ";
            if ($this->tokentype($a) == "alphanum") {
                if ($b[0] == '(') {
                	$a = "<span style='color:green;'><var>$a</var></span>";
                }
                else $a = "<var>$a</var>";
            }
            else if ($this->tokentype($a) == "value") {
            	$a = "<span style='color:blue;'>$a</span>";
            } else if ($this->tokentype($a) == "vector") {
            	$a = str_replace(array("{", "}"), "", $a);
            	$s = strlen($a)*1.2;
            	$a = " <span class=\"sym\"><var>$a</var>".
            		"<span class=\"vec\" style=\"transform:scale($s,1.0);\">&rarr;</span>".
            		"</span>";
            }
            $tokens[$i] = $a;
        }
        return $tokens;
    }
    private function copyarray($arg)
    {
    	$arrObject = new ArrayObject($arg);
        return $arrObject->getArrayCopy();
    }
    private function parsepar($tokens)
    {
        $tokens = $this->formatelement($tokens);
        $pos = 0;
        $len = count($tokens);
        $level = 0;
        while ($pos < $len)
        {
            $a = $tokens[$pos];
            while ($a != "(" && $pos < $len)
            {
                $pos++;
                $a = $tokens[$pos];
            }
            while ($a != ")" && $pos < $len)
            {
                if ($a == "(")
                {
                    $start = $pos;
                    $level++;
                }
                $pos++;
                $a = $tokens[$pos];
            }
            if ($a == ")")
            {
                $end = $pos;
                $level--;
                $subtokens = array_slice($tokens, $start + 1, $end - $start - 1);
                $subtokens = $this->copyarray($subtokens);
                array_splice($tokens, $start + 1, $end - $start);
                $arg = $this->exphtml($subtokens);
                
                // check function
                if ($this->tokentype(strip_tags($tokens[$start-1])) == "alphanum" && $start > 0){
                	$tokens[$start-1] = $this->testknownoperator($tokens[$start-1], $arg);
                	array_splice($tokens, $start, 1);
                	$pos = $start;
                	
                }
                else {
                	if (count($arg) == 1) {
                		$tokens[$start] = "(" . $arg[0] . ")";
                	} else {
                		$arrObject = new ArrayObject($arg);
                		$tokens[$start] = $this->copyarray($arg);
                	}
                	$pos = $start;
                }
                
                $len = count($tokens);
            }
            $pos++;
        }
        if ($level != 0) $tokens = $this->parsepar($tokens);
        return $tokens;
    }

    private function exphtml($tokens)
    {
        $len = count($tokens);
        if ($len == 1) {
            return $tokens;
        }
        // merge unary operator -
        for ($i = 0;$i < $len;$i++) {
            $a = $tokens[$i];
            $b = ($i < $len - 1) ? $tokens[$i + 1] : " ";
            $c = ($i < $len - 2) ? $tokens[$i + 2] : " ";
            if ($this->tokentype($a) == "operator") {
                if ($this->tokentype($b) == 'operator' && $b == '-') {
                	$tokens[$i+1] = $b . $c;
                	array_splice($tokens, $i + 2, 1);
                } else
                if ($a == '-' && $i == 0) {
                	$tokens[$i] = $a . $b;
                	array_splice($tokens, $i + 1, 1);
                }
            } else if ($this->tokentype(strip_tags($a)) == "alphanum" || $this->tokentype(strip_tags($a)) == "value") {
            	if ($this->tokentype($b) == 'operator' && $b == '!') {
                	$tokens[$i] = $a . $b;
                	array_splice($tokens, $i + 1, 1);
                } 
            }
        }
      
        $len = count($tokens);
        $pos = 0;
        while ($pos < $len - 2) {
            $a = $tokens[$pos];
            $o = $tokens[$pos + 1];
            $b = $tokens[$pos + 2];

            if ($this->tokentype($o) == "operator" && $o == '^') {
                $a = $this->mathhtml($a, $o, $b);
                $tokens[$pos] = $a;
                array_splice($tokens, $pos + 1, 2);
                $len = count($tokens);
            }
            else $pos += 2;
        }

        $pos = 0;
        while ($pos < $len - 2) {
            $a = $tokens[$pos];
            $o = $tokens[$pos + 1];
            $b = $tokens[$pos + 2];

            if ($this->tokentype($o) == "operator" && ($o == "/" || $o == "*")) {
                $a = $this->mathhtml($a, $o, $b);
                $tokens[$pos] = $a;
                array_splice($tokens, $pos + 1, 2);
                $len = count($tokens);
            }
            else $pos += 2;
        }

        $pos = 0;
        $a = $tokens[$pos];
        $arg = [];
        while ($pos < $len - 2) {
            $o = $tokens[$pos + 1];
            $b = $tokens[$pos + 2];
            
            if ($this->tokentype($o) == "operator")
            {
            	if ($o == ','){
            		$arg[] = $a;
            		$a = $b;
            	} else
                $a = $this->mathhtml($a, $o, $b);
            }
            $pos += 2;
        }
        
        if ($pos < $len-1 && $tokens[$pos+1][0] == '(') {
        	$a .= $tokens[$pos+1];
        }
        
        $arg[] = $a;
        return $arg;
    }

    public function printTokens()
    {
        echo "<pre>";
        foreach ($this->tokens as $token) {
            $token = trim($token);
            echo $token . "  is " . $this->tokentype($token) . "<br/>";
        }
        echo "</pre><br/>";
    }

    public function getHtml()
    {
        $tokens = $this->parsepar($this->tokens);
        $arg = $this->exphtml($tokens);
        return $arg[0];
    }
}

?>
