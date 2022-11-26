<?php
class mathExpHtml
{
    private $tokens;
    function __construct($str)
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
            ','
        ));
        $this->tokens = $this->str2tokens($str);
    }
    function operators()
    {
        return _OPERATORS;
    }
    function str2tokens($str)
    {
        $tokens = preg_split('/([<>()*\/=^+-])\s*|([\d.]+)\s*|(\w+)\s*|({\w+})\s*/', $str, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);
        return $tokens;
    }
    function tokentype($token)
    {
        return (is_numeric($token) ? "value" : (in_array($token, _OPERATORS) ? "operator" : (preg_match('/^[\w-]+$/', $token) ? "alphanum" : 
        	(preg_match('/({\w+})/', $token)?"vector":"none"))));
    }
    function mathhtml($a, $o, $b)
    {
        $html = "";
        switch ($o)
        {
            case '+':
            case '-':
            
            case '=':
            case '<':
            case '>':
            case ',':
                $html = "$a $o $b";
            break;
            case '*':
            $html = "$a • $b";
            break;
            case '^':
                $html = "$a<sup>$b</sup>";
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
    function testknownoperator($token){
    	$pattern = "/([(),+])\s*/";
    	//$str = strip_tags($token);
    	$test = preg_split($pattern, $token, 8, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);
    	
    	//echo "<br/>";
    	//print_r($test);
    	//echo "<br/>";
    	
    	$html = "";
    	
    	if (strip_tags($test[0]) == 'sum'){
    		$html = "<span class=\"intsuma\">".
    		"<span class=\"lim\">".$test[4]."</span>".
    		"<span class=\"sum-frac\">&sum;</span>".
    		"<span class=\"lim\">".$test[2]."</span>".
    		"</span>";
    	}/*
    	 else if (strip_tags($test[0]) == 'int'){
    		$html = "<span class=\"intsuma\">".
    		"<span class=\"lim\">".$test[4]."</span>".
    		"<span class=\"sum-frac\">&int;</span>".
    		"<span class=\"lim\">".$test[2]."</span>".
    		"</span>";
    	}
    	*/
    	return $html;
    }
    function formatelement($tokens)
    {
        $len = count($tokens);
        for ($i = 0;$i < $len;$i++)
        {
            $a = $tokens[$i];
            $b = ($i < $len - 1) ? trim($tokens[$i + 1]) : " ";
            if ($this->tokentype($a) == "alphanum")
            {
                if ($b[0] == '('){
                	$a = "<span style='color:green;'><var>$a</var></span>";
                }
                else $a = "<var>$a</var>";
            }
            else if ($this->tokentype($a) == "value"){
            	$a = "<span style='color:blue;'>$a</span>";
            } else if ($this->tokentype($a) == "vector"){
            	$a = str_replace(array("{", "}"), "", $a);
            	$s = strlen($a);
            	$a = " <span class=\"sym\"><var>$a</var>".
            		"<span class=\"vec\" style=\"transform:scale($s,1.0);\">&rarr;</span>".
            		"</span>";
            }
            $tokens[$i] = $a;
        }
        return $tokens;
    }
    
    function parsepar($tokens)
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
                array_splice($tokens, $start + 1, $end - $start);
                $tokens[$start] = "(" . $this->exphtml($subtokens) . ")";
                //echo "sub ".$subtokens[0]."=>".count($subtokens)."<br/>";
                $pos = $start;
            }
            $pos++;
        }
        /*
        print_r($tokens);
        echo "<br/>";
        print_r($subtokens);
        echo "<br/>";
        echo "level ".$level."<br/>";
        */
        if ($level != 0) $tokens = $this->parsepar($tokens);
        return $tokens;
    }
    function exphtml($tokens)
    {
        $len = count($tokens);
        
        if ($len <= 2)
        {
            return $tokens[0];
        }
        // merge function
        for ($i = 0;$i < $len;$i++)
        {
            $a = $tokens[$i];
            $b = ($i < $len - 1) ? trim($tokens[$i + 1]) : " ";
            if ($this->tokentype(strip_tags($a)) == "alphanum")
            {
                if ($b[0] == '('){
                	$tokens[$i] = $a . $b;
                	array_splice($tokens, $i + 1, 1);
                	$html = $this->testknownoperator($tokens[$i]);
                	if ($html != "") $tokens[$i] = $html;
                }
            }
        }
        $len = count($tokens);
        //echo "length ".$len."<br/>";
        $pos = 0;
        while ($pos < $len - 2)
        {
            $a = $tokens[$pos];
            $o = $tokens[$pos + 1];
            $b = $tokens[$pos + 2];

            if ($this->tokentype($o) == "operator" && $o == '^')
            {
                $a = $this->mathhtml($a, $o, $b);
                $tokens[$pos] = $a;
                array_splice($tokens, $pos + 1, 2);
                $len = count($tokens);
            }
            else $pos += 2;
        }

        $pos = 0;
        while ($pos < $len - 2)
        {
            $a = $tokens[$pos];
            $o = $tokens[$pos + 1];
            $b = $tokens[$pos + 2];

            if ($this->tokentype($o) == "operator" && ($o == "/" || $o == "*"))
            {
                $a = $this->mathhtml($a, $o, $b);
                $tokens[$pos] = $a;
                array_splice($tokens, $pos + 1, 2);
                $len = count($tokens);
            }
            else $pos += 2;
        }

        $pos = 0;
        $a = $tokens[$pos];
        while ($pos < $len - 2)
        {
            $o = $tokens[$pos + 1];
            $b = $tokens[$pos + 2];
            
            if ($this->tokentype($o) == "operator")
            {
                $a = $this->mathhtml($a, $o, $b);
            }
            $pos += 2;
        }
        if ($pos < $len-1 && $tokens[$pos+1][0] == '(') {
        	$a .= $tokens[$pos+1];
        }
        
        return $a;
    }
    function printtokens()
    {
        echo "<pre>";
        foreach ($this->tokens as $token)
        {
            $token = trim($token);
            echo $token . "  is " . $this->tokentype($token) . "<br/>";
        }
        echo "</pre>";
        echo "<br/>";
    }
    function gethtml()
    {
        $tokens = $this->parsepar($this->tokens);
        //echo "<br/>";
        //print_r($tokens);
        //echo "<br/>";
        echo "" . $this->exphtml($tokens) . "";
    }
}

?>
