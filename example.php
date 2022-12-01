<?php
/**
 * mathExpHtml Class
 *
 * This class is free for the educational use as long as maintain this header together with this class.
 * Author: Win Aung Cho
 * Contact winaungcho@gmail.com
 * version 1.3
 * Date: 1-12-2022
 */
include("mathexphtml.php");
?>
	<html>

	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width,initial-scale=0.30,maximum-scale=1.60">
		<style>
		body {
			position: absolute;
			width: 100%;
			height: 100%;
			top: 50%;
			left: 50%;
			transform: translate(-50%, -50%);
			font-size: 2rem;
			text-align: center;
			font-family: "Times New Roman", Times, serif;
		}
		
		.math {
			text-align: center;
			padding: 0em 0em;
			font-size: 2rem;
		}
		
		.math .intsuma {
			position: relative;
			display: inline-block;
			vertical-align: middle;
			text-align: center;
		}
		
		.math .intsuma > span {
			display: block;
			font-size: 70%;
		}
		
		.math .intsuma .lim-up {
			margin-bottom: -1ex;
		}
		
		.math .intsuma .lim {
			margin-top: -0.5ex;
		}
		
		.math .intsuma .sum {
			font-size: 1.5em;
			font-weight: lighter;
		}
		
		.math .intsuma .sum-frac {
			font-size: 1.5em;
			font-weight: 100;
		}
		
		.math .fraction {
			display: inline-block;
			vertical-align: middle;
			margin: 0 0.2em 0.4ex;
			text-align: center;
		}
		
		.math .fraction > span {
			display: block;
			padding-top: 0.15em;
		}
		
		.math .fraction span.denom {
			border-top: solid 2px black;
		}
		
		.math .fraction span.bar {
			display: none;
		}
		
		.math .sym {
			position: relative;
			text-align: center;
		}
		
		.math .vec {
			position: absolute;
			top: -0.8em;
			left: 0px;
			width: 100%;
			
			text-align: center;
			text-transform: full-width;
		}
		
		.math .matrix {
			vertical-align: middle;
			text-align: center;
			font-size: 70%;
			position: relative;
			display: inline-block;
		}
		.math .matrix td{
			font-size: 1.6rem;
		}
		.math .matrix:before,
		.matrix:after {
			content: "";
			position: absolute;
			top: 0;
			border: 1px solid #000;
			width: 6px;
			height: 100%;
		}
		
		.math .matrix:before {
			left: -6px;
			border-right: 0;
		}
		
		.math .matrix:after {
			right: -6px;
			border-left: 0;
		}
		</style>
	</head>

	<body>
		<div style="overflow-x:auto;overflow-y:auto; width:100%;height:100%;white-space: nowrap;">
			<div class="math">
				<?php
				
				//$str = "sin(3+π/4*i)/(2-3*π^2)";
				$str = "a=sum(i=1,3,23.5*{vec}-(((atan(2+θ/2,-7)+asdθ9[2)^-2)/4.5+sin(0.5*θ))/(20.06+π*(16))^2-26)";
				$parser = new mathExpHtml($str);
				echo $parser->getHtml()."<br/>";
				
				$str = "-b±(b^2-14*a*c)^0.5/(2*a)";
				$parser = new mathExpHtml($str);
				echo $parser->getHtml()."<br/>";

				$str = "sum(8, 12, 5+2*x*(θ-6/x)^2)";
				$parser = new mathExpHtml($str);
				echo $parser->getHtml()."<br/>";

				$str = "int(8, 12, 5+2*x*(θ-6/x)^2, dx)";
				$parser = new mathExpHtml($str);
				echo $parser->getHtml()."<br/>";
				echo "<br/>";
				
				$str = "A=-x^-3/3!+x^2/2!";
				$parser = new mathExpHtml($str);
				//$parser->printTokens();
				echo $parser->getHtml()."<br/>";
				echo "<br/>";
				
				$str = "{M}={vec}×((2*a,5,a[2),(6,2,4),(1,6,8))×(1,2,3)";
				$parser = new mathExpHtml($str);
				echo $parser->getHtml()."<br/>";
			?>
			</div>
		</div>
	</body>

	</html>
