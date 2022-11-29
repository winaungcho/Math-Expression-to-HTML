<?php
include("mathexphtml.php");

/*
 * mathExpHtml Class and example file
 *
 * This class is free for the educational use as long as maintain this header together with this class.
 * Author: Win Aung Cho
 * Contact winaungcho@gmail.com
 * version 1.0
 * Date: 26-11-2022
 */
?>

<html lang="en">
	<head>
		<meta charset="utf-8">
		<style>
		body {
			position: absolute;
			width:100%;
			height:100%;
			top: 50%;
			left: 50%;
			transform: translate(-50%, -50%);
			font-size: 2rem;
			text-align: center;
			font-family: "Times New Roman", Times, serif;
		}
		
		.intsuma {
			position: relative;
			display: inline-block;
			vertical-align: middle;
			text-align: center;
		}
		
		.intsuma > span {
			display: block;
			font-size: 70%;
		}
		
		.intsuma .lim-up {
			margin-bottom: -1ex;
		}
		
		.intsuma .lim {
			margin-top: -0.5ex;
		}
		
		.intsuma .sum {
			font-size: 1.5em;
			font-weight: lighter;
		}
		
		.intsuma .sum-frac {
			font-size: 1.5em;
			font-weight: 100;
		}
		
		.fraction {
			display: inline-block;
			vertical-align: middle;
			margin: 0 0.2em 0.4ex;
			text-align: center;
		}
		
		.fraction > span {
			display: block;
			padding-top: 0.15em;
		}
		
		.fraction span.denom {
			border-top: solid 2px black;
		}
		
		.fraction span.bar {
			display: none;
		}
		.sym {
			position: relative;
			text-align: center;
		}
		.vec{
			position: absolute;
			top: -0.8em;
			left: 0px;
			width: 100%;
			font-size: 70%;
			text-align: center;
			text-transform: full-width;
		}
		</style>
	</head>

	<body>
		<div style="overflow-x:auto;overflow-y:auto; width:100%;height:100%;white-space: nowrap;">
			<?php
				$str = "a=sum(i=1,3,23.5*{vec}-(((atan(2+θ/2,7)+asdθ9[2)^2)/4.5+sin(0.5*θ))/(20.06+π*(16))^2-26)";
				$parser = new mathExpHtml($str);
				$parser->printTokens();
				echo $parser->getHtml();
				$str = "-b±(b^2-14*a*c)^0.5/(2*a)";
				$parser = new mathExpHtml($str);
				echo $parser->getHtml()."<br/>";

				$str = "sum(8, 12, 5+2*x*(θ-6/x)^2)";
				$parser = new mathExpHtml($str);
				echo $parser->getHtml()."<br/>";

				$str = "int(8, 12, 5+2*x*(θ-6/x)^2, dx)";
				$parser = new mathExpHtml($str);
				echo $parser->getHtml()."<br/>";
			?>
		</div>
	</body>
</html>
