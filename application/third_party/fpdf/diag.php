<?php
require('sector.php');

class PDF_Diag extends PDF_Sector {
	var $legends;
	var $wLegend;
	var $sum;
	var $NbVal;

	function PieChart($w, $h, $data, $format, $colors=null)
	{
		$this->SetFont('Courier', '', 10);
		$this->SetLegends($data,$format);

		$XPage = $this->GetX();
		$YPage = $this->GetY();
		$margin = 2;
		$hLegend = 5;
		$radius = min($w - $margin * 4 - $hLegend - $this->wLegend, $h - $margin * 2);
		$radius = floor($radius / 2);
		$XDiag = $XPage + $margin + $radius;
		$YDiag = $YPage + $margin + $radius;
		if($colors == null) {
			for($i = 0; $i < $this->NbVal; $i++) {
				$gray = $i * intval(255 / $this->NbVal);
				$colors[$i] = array($gray,$gray,$gray);
			}
		}

		//Sectors
		$this->SetLineWidth(0.2);
		$angleStart = 0;
		$angleEnd = 0;
		$i = 0;
		foreach($data as $val) {
			$angle = ($val * 360) / doubleval($this->sum);
			if ($angle != 0) {
				$angleEnd = $angleStart + $angle;
				$this->SetFillColor($colors[$i][0],$colors[$i][1],$colors[$i][2]);
				$this->Sector($XDiag, $YDiag, $radius, $angleStart, $angleEnd);
				$angleStart += $angle;
			}
			$i++;
		}

		//Legends
		$this->SetFont('Courier', '', 10);
		$x1 = $XPage + 2 * $radius + 4 * $margin;
		$x2 = $x1 + $hLegend + $margin;
		$y1 = $YDiag - $radius + (2 * $radius - $this->NbVal*($hLegend + $margin)) / 2;
		for($i=0; $i<$this->NbVal; $i++) {
			$this->SetFillColor($colors[$i][0],$colors[$i][1],$colors[$i][2]);
			$this->Rect($x1, $y1, $hLegend, $hLegend, 'DF');
			$this->SetXY($x2,$y1);
			$this->Cell(0,$hLegend,$this->legends[$i]);
			$y1+=$hLegend + $margin;
		}
	}

	function BarDiagram($w, $h, $data, $format, $color=null, $maxVal=0, $nbDiv=4)
	{
		$this->SetFont('Courier', '', 10);
		$this->SetLegends($data,$format);

		$XPage = $this->GetX();
		$YPage = $this->GetY();
		$margin = 2;
		$YDiag = $YPage + $margin;
		$hDiag = floor($h - $margin * 2);
		$XDiag = $XPage + $margin * 2 + $this->wLegend;
		$lDiag = floor($w - $margin * 3 - $this->wLegend);
		if($color == null)
			$color=array(155,155,155);
		if ($maxVal == 0) {
			$maxVal = max($data);
		}
		$valIndRepere = ceil($maxVal / $nbDiv);
		$maxVal = $valIndRepere * $nbDiv;
		$lRepere = floor($lDiag / $nbDiv);
		$lDiag = $lRepere * $nbDiv;
		$unit = $lDiag / $maxVal;
		$hBar = floor($hDiag / ($this->NbVal + 1));
		$hDiag = $hBar * ($this->NbVal + 1);
		$eBaton = floor($hBar * 80 / 100);

		$this->SetLineWidth(0.2);
		$this->Rect($XDiag, $YDiag, $lDiag, $hDiag);

		$this->SetFont('Courier', '', 10);
		$this->SetFillColor($color[0],$color[1],$color[2]);
		$i=0;
		foreach($data as $val) {
			//Bar
			$xval = $XDiag;
			$lval = (int)($val * $unit);
			$yval = $YDiag + ($i + 1) * $hBar - $eBaton / 2;
			$hval = $eBaton;
			$this->Rect($xval, $yval, $lval, $hval, 'DF');
			//Legend
			$this->SetXY(0, $yval);
			$this->Cell($xval - $margin, $hval, $this->legends[$i],0,0,'R');
			$i++;
		}

		//Scales
		for ($i = 0; $i <= $nbDiv; $i++) {
			$xpos = $XDiag + $lRepere * $i;
			$this->Line($xpos, $YDiag, $xpos, $YDiag + $hDiag);
			$val = $i * $valIndRepere;
			$xpos = $XDiag + $lRepere * $i - $this->GetStringWidth($val) / 2;
			$ypos = $YDiag + $hDiag - $margin;
			$this->Text($xpos, $ypos, $val);
		}
	}

	function ColumnChart($w, $h, $data, $format, $color=null, $maxVal=0, $nbDiv=4)
	{

        // RGB for color 0
		$colors[0][0] = 155;
		$colors[0][1] = 75;
		$colors[0][2] = 155;

        // RGB for color 1
		$colors[1][0] = 0;
		$colors[1][1] = 155;
		$colors[1][2] = 0;

        // RGB for color 2
		$colors[2][0] = 75;
		$colors[2][1] = 155;
		$colors[2][2] = 255;

        // RGB for color 3
		$colors[3][0] = 75;
		$colors[3][1] = 0;
		$colors[3][2] = 155;

		$this->SetFont('Arial', '', 9);
		$this->SetLegends($data,$format);

        // Starting corner (current page position where the chart has been inserted)
		$XPage = $this->GetX();
		$YPage = $this->GetY();
		$margin = 2; 
		$YDiag = $YPage + $margin;
		$hDiag = floor($h - $margin * 2);
		$XDiag = $XPage + $margin * 2 + $this->wLegend;
		$lDiag = floor($w - $margin * 3 - $this->wLegend);

		if($color == null)
			$color=array(155,155,155);
		if ($maxVal == 0) 
		{
			foreach($data as $val)
			{
				if(max($val) > $maxVal)
				{
					$maxVal = max($val);
				}
			}
		}

        // define the distance between the visual reference lines (the lines which cross the chart's internal area and serve as visual reference for the column's heights)
		$valIndRepere = ceil($maxVal / $nbDiv);

        // adjust the maximum value to be plotted (recalculate through the newly calculated distance between the visual reference lines)
		$maxVal = $valIndRepere * $nbDiv;

        // define the distance between the visual reference lines (in milimeters)
		$hRepere = floor($hDiag / $nbDiv);

        // adjust the chart HEIGHT
		$hDiag = $hRepere * $nbDiv;

        // determine the height unit (milimiters/data unit)
		$unit = $hDiag / $maxVal;

        // determine the bar's thickness
		$lBar = floor($lDiag / ($this->NbVal + 1));
		$lDiag = $lBar * ($this->NbVal + 1);
		$eColumn = floor($lBar * 80 / 100);

        // draw the chart border
		$this->SetLineWidth(0.2);
		$this->Rect($XDiag, $YDiag, $lDiag, $hDiag);

		$this->SetFont('Arial', '', 9);
		$this->SetFillColor($color[0],$color[1],$color[2]);
		$i=0;
		foreach($data as $val) 
		{
            //Column
			$yval = $YDiag + $hDiag;
			$xval = $XDiag + ($i + 1) * $lBar - $eColumn/2;
			$lval = floor($eColumn/(count($val)));
			$j=0;
			foreach($val as $v)
			{
				$hval = (int)($v * $unit);
				$this->SetFillColor($colors[$j][0], $colors[$j][1], $colors[$j][2]);
				$this->Rect($xval+($lval*$j), $yval, $lval, -$hval, 'DF');
				$j++;
			}

			$i++;
		}

        //Scales
		for ($i = 0; $i <= $nbDiv; $i++) 
		{
			$ypos = $YDiag + $hRepere * $i;
			$this->Line($XDiag, $ypos, $XDiag + $lDiag, $ypos);
			$val = ($nbDiv - $i) * $valIndRepere;
			$ypos = $YDiag + $hRepere * $i;
			$xpos = $XDiag - $margin - $this->GetStringWidth($val);
			$this->Text($xpos, $ypos, $val);
		}
	}

	function ColumnChart2($w, $h, $data, $format, $color=null, $maxVal=0, $nbDiv=4)
	{

        // RGB for color 0
		$colors[0][0] = 155;
		$colors[0][1] = 75;
		$colors[0][2] = 155;

        // RGB for color 1
		$colors[1][0] = 0;
		$colors[1][1] = 155;
		$colors[1][2] = 0;

        // RGB for color 2
		$colors[2][0] = 75;
		$colors[2][1] = 155;
		$colors[2][2] = 255;

        // RGB for color 3
		$colors[3][0] = 75;
		$colors[3][1] = 0;
		$colors[3][2] = 155;

		$this->SetFont('Arial', '', 9);
		$this->SetLegends2($data,$format);

        // Starting corner (current page position where the chart has been inserted)
		$XPage = $this->GetX();
		$YPage = $this->GetY();
		$margin = 2; 
		$YDiag = $YPage + $margin;
		$hDiag = floor($h - $margin * 2);
		$XDiag = $XPage + $margin * 2 + $this->wLegend;
		$lDiag = floor($w - $margin * 3 - $this->wLegend);

		if($color == null)
			$color=array(155,155,155);
		if ($maxVal == 0) 
		{
			foreach($data as $val)
			{
				if(max($val) > $maxVal)
				{
					$maxVal = max($val);
				}
			}
		}

        // define the distance between the visual reference lines (the lines which cross the chart's internal area and serve as visual reference for the column's heights)
		$valIndRepere = ceil($maxVal / $nbDiv);

        // adjust the maximum value to be plotted (recalculate through the newly calculated distance between the visual reference lines)
		$maxVal = $valIndRepere * $nbDiv;

        // define the distance between the visual reference lines (in milimeters)
		$hRepere = floor($hDiag / $nbDiv);

        // adjust the chart HEIGHT
		$hDiag = $hRepere * $nbDiv;

        // determine the height unit (milimiters/data unit)
		$unit = $hDiag / $maxVal;

        // determine the bar's thickness
		$lBar = floor($lDiag / ($this->NbVal + 1));
		$lDiag = $lBar * ($this->NbVal + 1);
		$eColumn = floor($lBar * 80 / 100);

        // draw the chart border
		$this->SetLineWidth(0.2);
		$this->Rect($XDiag, $YDiag, $lDiag, $hDiag);

		$this->SetFont('Arial', '', 9);
		$this->SetFillColor($color[0],$color[1],$color[2]);
		$i=0;
		foreach($data as $val) 
		{
            //Column
			$yval = $YDiag + $hDiag;
			$xval = $XDiag + ($i + 1) * $lBar - $eColumn/2;
			$lval = floor($eColumn/(count($val)));
			$j=0;
			foreach($val as $v)
			{
				$hval = (int)($v * $unit);
				$this->SetFillColor($colors[$j][0], $colors[$j][1], $colors[$j][2]);
				$this->Rect($xval+($lval*$j), $yval, $lval, -$hval, 'DF');
				$j++;
			}

			$i++;
		}

        //Scales
		for ($i = 0; $i <= $nbDiv; $i++) 
		{
			$ypos = $YDiag + $hRepere * $i;
			$this->Line($XDiag, $ypos, $XDiag + $lDiag, $ypos);
			$val = ($nbDiv - $i) * $valIndRepere;
			$ypos = $YDiag + $hRepere * $i;
			$xpos = $XDiag - $margin - $this->GetStringWidth($val);
			$this->Text($xpos, $ypos, $val);
		}
	}

	function SetLegends($data, $format)
	{
		$this->legends=array();
		$this->wLegend=0;
		$this->sum=array_sum($data);
		$this->NbVal=count($data);
		foreach($data as $l=>$val)
		{
			$p=sprintf('%.2f',$val/$this->sum*100).'%';
			$legend=str_replace(array('%l','%v','%p'),array($l,$val,$p),$format);
			$this->legends[]=$legend;
			$this->wLegend=max($this->GetStringWidth($legend),$this->wLegend);
		}
	}

	function SetLegends2($data, $format)
	{
		$this->legends=array();
		$this->wLegend=0;
		$this->sum=array_sum($data);
		$this->NbVal=count($data);
		foreach($data as $l=>$val)
		{
			$p=sprintf('%.2f',$this->sum*100).'%';
			$legend=str_replace(array('%l','%v','%p'),array($l,$val,$p),$format);
			$this->legends[]=$legend;
			$this->wLegend=max($this->GetStringWidth($legend),$this->wLegend);
		}
	}
}
