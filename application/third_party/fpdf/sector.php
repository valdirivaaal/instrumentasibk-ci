<?php
require('fpdf.php');

class PDF_Sector extends FPDF
{
	var $heights;
	var $widths;
	var $aligns;
	public $tablewidths;
	public $footerset;

	function SetHeights($he)
	{
    //Set the array of column widths
		$this->heights=$he;
	}

	function SetWidths($w)
	{
    //Set the array of column widths
		$this->widths=$w;
	}

	function SetAligns($a)
	{
    //Set the array of column alignments
		$this->aligns=$a;
	}

	function Row($data)
	{
    //Calculate the height of the row
		$nb=0;
		for($i=0;$i<count($data);$i++)
			$nb=max($nb,$this->NbLines($this->widths[$i],$data[$i]));
		$h=5*$nb;
    //Issue a page break first if needed
		$this->CheckPageBreak($h);
    //Draw the cells of the row
		for($i=0;$i<count($data);$i++)
		{
			$w=$this->widths[$i];
			$he=$this->heights[$i];
			$a=isset($this->aligns[$i]) ? $this->aligns[$i] : 'L';
        //Save the current position
			$x=$this->GetX();
			$y=$this->GetY();
        //Draw the border
			$this->Rect($x,$y,$w,$h);

			$this->MultiCell($w,5,$data[$i],0,$a);
        //Put the position to the right of the cell
			$this->SetXY($x+$w,$y);
		}
    //Go to the next line
		$this->Ln($h);
	}

	function CheckPageBreak($h)
	{
    //If the height h would cause an overflow, add a new page immediately
		if($this->GetY()+$h>$this->PageBreakTrigger)
			$this->AddPage($this->CurOrientation);
	}

	function NbLines($w,$txt)
	{
    //Computes the number of lines a MultiCell of width w will take
		$cw=&$this->CurrentFont['cw'];
		if($w==0)
			$w=$this->w-$this->rMargin-$this->x;
		$wmax=($w-2*$this->cMargin)*1000/$this->FontSize;
		$s=str_replace("\r",'',$txt);
		$nb=strlen($s);
		if($nb>0 and $s[$nb-1]=="\n")
			$nb--;
		$sep=-1;
		$i=0;
		$j=0;
		$l=0;
		$nl=1;
		while($i<$nb)
		{
			$c=$s[$i];
			if($c=="\n")
			{
				$i++;
				$sep=-1;
				$j=$i;
				$l=0;
				$nl++;
				continue;
			}
			if($c==' ')
				$sep=$i;
			$l+=$cw[$c];
			if($l>$wmax)
			{
				if($sep==-1)
				{
					if($i==$j)
						$i++;
				}
				else
					$i=$sep+1;
				$sep=-1;
				$j=$i;
				$l=0;
				$nl++;
			}
			else
				$i++;
		}
		return $nl+0.5;
	}

	function Sector($xc, $yc, $r, $a, $b, $style='FD', $cw=true, $o=90)
	{
		$d0 = $a - $b;
		if($cw){
			$d = $b;
			$b = $o - $a;
			$a = $o - $d;
		}else{
			$b += $o;
			$a += $o;
		}
		while($a<0)
			$a += 360;
		while($a>360)
			$a -= 360;
		while($b<0)
			$b += 360;
		while($b>360)
			$b -= 360;
		if ($a > $b)
			$b += 360;
		$b = $b/360*2*M_PI;
		$a = $a/360*2*M_PI;
		$d = $b - $a;
		if ($d == 0 && $d0 != 0)
			$d = 2*M_PI;
		$k = $this->k;
		$hp = $this->h;
		if (sin($d/2))
			$MyArc = 4/3*(1-cos($d/2))/sin($d/2)*$r;
		else
			$MyArc = 0;
		//first put the center
		$this->_out(sprintf('%.2F %.2F m',($xc)*$k,($hp-$yc)*$k));
		//put the first point
		$this->_out(sprintf('%.2F %.2F l',($xc+$r*cos($a))*$k,(($hp-($yc-$r*sin($a)))*$k)));
		//draw the arc
		if ($d < M_PI/2){
			$this->_Arc($xc+$r*cos($a)+$MyArc*cos(M_PI/2+$a),
				$yc-$r*sin($a)-$MyArc*sin(M_PI/2+$a),
				$xc+$r*cos($b)+$MyArc*cos($b-M_PI/2),
				$yc-$r*sin($b)-$MyArc*sin($b-M_PI/2),
				$xc+$r*cos($b),
				$yc-$r*sin($b)
			);
		}else{
			$b = $a + $d/4;
			$MyArc = 4/3*(1-cos($d/8))/sin($d/8)*$r;
			$this->_Arc($xc+$r*cos($a)+$MyArc*cos(M_PI/2+$a),
				$yc-$r*sin($a)-$MyArc*sin(M_PI/2+$a),
				$xc+$r*cos($b)+$MyArc*cos($b-M_PI/2),
				$yc-$r*sin($b)-$MyArc*sin($b-M_PI/2),
				$xc+$r*cos($b),
				$yc-$r*sin($b)
			);
			$a = $b;
			$b = $a + $d/4;
			$this->_Arc($xc+$r*cos($a)+$MyArc*cos(M_PI/2+$a),
				$yc-$r*sin($a)-$MyArc*sin(M_PI/2+$a),
				$xc+$r*cos($b)+$MyArc*cos($b-M_PI/2),
				$yc-$r*sin($b)-$MyArc*sin($b-M_PI/2),
				$xc+$r*cos($b),
				$yc-$r*sin($b)
			);
			$a = $b;
			$b = $a + $d/4;
			$this->_Arc($xc+$r*cos($a)+$MyArc*cos(M_PI/2+$a),
				$yc-$r*sin($a)-$MyArc*sin(M_PI/2+$a),
				$xc+$r*cos($b)+$MyArc*cos($b-M_PI/2),
				$yc-$r*sin($b)-$MyArc*sin($b-M_PI/2),
				$xc+$r*cos($b),
				$yc-$r*sin($b)
			);
			$a = $b;
			$b = $a + $d/4;
			$this->_Arc($xc+$r*cos($a)+$MyArc*cos(M_PI/2+$a),
				$yc-$r*sin($a)-$MyArc*sin(M_PI/2+$a),
				$xc+$r*cos($b)+$MyArc*cos($b-M_PI/2),
				$yc-$r*sin($b)-$MyArc*sin($b-M_PI/2),
				$xc+$r*cos($b),
				$yc-$r*sin($b)
			);
		}
		//terminate drawing
		if($style=='F')
			$op='f';
		elseif($style=='FD' || $style=='DF')
			$op='b';
		else
			$op='s';
		$this->_out($op);
	}

	function _Arc($x1, $y1, $x2, $y2, $x3, $y3 )
	{
		$h = $this->h;
		$this->_out(sprintf('%.2F %.2F %.2F %.2F %.2F %.2F c',
			$x1*$this->k,
			($h-$y1)*$this->k,
			$x2*$this->k,
			($h-$y2)*$this->k,
			$x3*$this->k,
			($h-$y3)*$this->k));
	}

	function _beginpage($orientation, $size, $rotation) {
		$this->page++;
    if(!isset($this->pages[$this->page])) // solves the problem of overwriting a page if it already exists
    $this->pages[$this->page] = '';
    $this->state = 2;
    $this->x = $this->lMargin;
    $this->y = $this->tMargin;
    $this->FontFamily = '';
    // Check page size and orientation
    if($orientation=='')
    	$orientation = $this->DefOrientation;
    else
    	$orientation = strtoupper($orientation[0]);
    if($size=='')
    	$size = $this->DefPageSize;
    else
    	$size = $this->_getpagesize($size);
    if($orientation!=$this->CurOrientation || $size[0]!=$this->CurPageSize[0] || $size[1]!=$this->CurPageSize[1])
    {
        // New size or orientation
    	if($orientation=='P')
    	{
    		$this->w = $size[0];
    		$this->h = $size[1];
    	}
    	else
    	{
    		$this->w = $size[1];
    		$this->h = $size[0];
    	}
    	$this->wPt = $this->w*$this->k;
    	$this->hPt = $this->h*$this->k;
    	$this->PageBreakTrigger = $this->h-$this->bMargin;
    	$this->CurOrientation = $orientation;
    	$this->CurPageSize = $size;
    }
    if($orientation!=$this->DefOrientation || $size[0]!=$this->DefPageSize[0] || $size[1]!=$this->DefPageSize[1])
    	$this->PageInfo[$this->page]['size'] = array($this->wPt, $this->hPt);
    if($rotation!=0)
    {
    	if($rotation%90!=0)
    		$this->Error('Incorrect rotation value: '.$rotation);
    	$this->CurRotation = $rotation;
    	$this->PageInfo[$this->page]['rotation'] = $rotation;
    }
}

function Footer() {
    // Check if Footer for this page already exists (do the same for Header())
	if(!isset($this->footerset[$this->page])) {
		$this->SetY(-15);
        // Page number
		// $this->Cell(0,10,'Page '.$this->PageNo().'/{nb}',0,0,'C');
        // set footerset
		$this->footerset[$this->page] = true;
	}
}

function morepagestable($datas, $lineheight=8) {
    // some things to set and 'remember'
	$l = $this->lMargin;
	$startheight = $h = $this->GetY();
	$startpage = $currpage = $maxpage = $this->page;

    // calculate the whole width
	$fullwidth = 0;
	foreach($this->tablewidths AS $width) {
		$fullwidth += $width;
	}

    // Now let's start to write the table
	foreach($datas AS $row => $data) {
		$this->page = $currpage;
        // write the horizontal borders
		$this->Line($l,$h,$fullwidth+$l,$h);
        // write the content and remember the height of the highest col
		$i = 0;
		foreach($data AS $col => $txt) {
			$this->page = $currpage;
			$this->SetXY($l,$h);
			$a=isset($this->aligns[$i]) ? $this->aligns[$i] : 'L';
			$this->MultiCell($this->tablewidths[$col],$lineheight,$txt,0,$a);
			$l += $this->tablewidths[$col];

			if(!isset($tmpheight[$row.'-'.$this->page]))
				$tmpheight[$row.'-'.$this->page] = 0;
			if($tmpheight[$row.'-'.$this->page] < $this->GetY()) {
				$tmpheight[$row.'-'.$this->page] = $this->GetY();
			}
			if($this->page > $maxpage)
				$maxpage = $this->page;
			$i =+ 1;
		}

        // get the height we were in the last used page
		$h = $tmpheight[$row.'-'.$maxpage];
        // set the "pointer" to the left margin
		$l = $this->lMargin;
        // set the $currpage to the last page
		$currpage = $maxpage;
	}
    // draw the borders
    // we start adding a horizontal line on the last page
	$this->page = $maxpage;
	$this->Line($l,$h,$fullwidth+$l,$h);
    // now we start at the top of the document and walk down
	for($i = $startpage; $i <= $maxpage; $i++) {
		$this->page = $i;
		$l = $this->lMargin;
		$t  = ($i == $startpage) ? $startheight : $this->tMargin;
		$lh = ($i == $maxpage)   ? $h : $this->h-$this->bMargin;
		$this->Line($l,$t,$l,$lh);
		foreach($this->tablewidths AS $width) {
			$l += $width;
			$this->Line($l,$t,$l,$lh);
		}
	}
    // set it to the last page, if not it'll cause some problems
	$this->page = $maxpage;
}
}
?>
