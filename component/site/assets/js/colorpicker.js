// No Mootools used
// // BASED ON:
//---------------------------------------------------------
// Color Picker Script from Flooble.com
// For more information, visit
// http://www.flooble.com/scripts/colorpicker.php
// Copyright 2003 Animus Pactum Consulting inc.
// edited by mic [ http://www.mgfi.info ] - 2006.07.29
//---------------------------------------------------------

function ColorPicker(varname, id, color, txt_nocolor) {
	this.picker = null;
// METHODS
     this.pickColor = function () {
     	if (!this.picker) { this.setDiv(); }
		if (this.picker.style.display == 'block' || this.picker.style.display == 'table' || this.picker.style.display == 'inline') {
			this.picker.style.display = 'none';
			this.hideShowCovered();
			return;
		}
		pos = this.getAbsolutePos(this.field);
		//alert(this.field.id + ' :: '+ pos.x+','+pos.y);
     	this.picker.style.top = (pos.y + this.field.offsetHeight)+'px';
     	this.picker.style.left = pos.x+'px';
     		// begin [mic ] had to cheat here, otherwise the color-window will be behind and invisible!
     		//this.picker.style.top = '-730px';
     		//this.picker.style.left = '130px';
     		// end [ mic ]
		this.picker.style.display = 'inline'; // block, inline (original), table, table-row, table-cell, table-caption
		this.hideShowCovered();
	}

	 this.getObj = function ( id ) {
		if (this.ie) { return document.all[id]; }
		else {	return document.getElementById(id);	}
	 }

	this.getAbsolutePos = function (el) {
		var fuse = 100;
		var r = { x: 0, y: 0 };
		do {
			r.y += el.offsetTop;
			r.x += el.offsetLeft;
			el = el.offsetParent;
			if (--fuse <= 0) break;
		}
		while (el);
		if (fuse <= 0) {
			alert('Fuse blew');
		}
		return r;
	}

	this.addColor = function (r, g, b) {
		this.addColorValue(this.colorLevels[r], this.colorLevels[g], this.colorLevels[b]);
	}

	this.addColorValue = function(r, g, b) {
		this.colorArray[this.colorArray.length] = '#' + r + r + g + g + b + b;
	}

	this.setColor = function ( color ) {
		this.field.value = color;
		if (color == '') {
			this.swatch.style.background = this.nocolor;
			this.swatch.style.color =  this.nocolor;
			color =  this.nocolor;
		} else {
			this.swatch.style.background = color;
			this.swatch.style.color = color;
		}
		this.picker.style.display = 'none';
		//eval(this.field.title);
	}

	this.setDiv = function () {
		if (!document.createElement) { return; }
		var elemDiv = document.createElement('div');
		if (typeof(elemDiv.innerHTML) != 'string') { return; }
		this.genColors();
		elemDiv.id = this.pickername;
		elemDiv.style.position = 'absolute'; // relative | absolute [original value] [mic]
		elemDiv.style.display = 'none';
		elemDiv.style.border = '#000000 1px solid';
		elemDiv.style.background = '#FFFFFF';
		elemDiv.onclick = function() {this.style.display = 'none';}
		elemDiv.innerHTML = '<span style="font-family:Verdana; font-size:11px;">'
			+ '<a href="javascript:'+this.varname+'.setColor(\'\');">' + txt_nocolor +'</a></span>'
			+ this.getColorTable()
			// + '</span></div>'
			//+ '<center><a href="http://www.flooble.com/scripts/colorpicker.php"'
			//+ ' target="_blank">color picker by <b>flooble</b></a></center></span>'
		;

		document.body.appendChild(elemDiv);
		this.picker = this.getObj(this.pickername);
		//xshow(this.picker);
	}

	this.genColors = function () {
		this.addColorValue('0','0','0');
		this.addColorValue('1','1','1');
		this.addColorValue('2','2','2');
		this.addColorValue('3','3','3');
		this.addColorValue('4','4','4');
		this.addColorValue('5','5','5');
		this.addColorValue('6','6','6');
		this.addColorValue('7','7','7');
		this.addColorValue('8','8','8');
		this.addColorValue('9','9','9');
		this.addColorValue('A','A','A');
		this.addColorValue('B','B','B');
		this.addColorValue('C','C','C');
		this.addColorValue('D','D','D');
		this.addColorValue('E','E','E');
		this.addColorValue('F','F','F');

		var n = this.colorLevels.length
		for (a = 1; a < n; a++) this.addColor(0,0,a);
		for (a = 1; a < n-1; a++) this.addColor(a,a,5);

		for (a = 1; a < n; a++) this.addColor(0,a,0);
		for (a = 1; a < n-1; a++) this.addColor(a,5,a);

		for (a = 1; a < n; a++) this.addColor(a,0,0);
		for (a = 1; a < n-1; a++) this.addColor(5,a,a);


		for (a = 1; a < n; a++) this.addColor(a,a,0);
		for (a = 1; a < n-1; a++) this.addColor(5,5,a);

		for (a = 1; a < n; a++) this.addColor(0,a,a);
		for (a = 1; a < n-1; a++) this.addColor(a,5,5);

		for (a = 1; a < this.n; a++) this.addColor(a,0,a);
		for (a = 1; a < this.n-1; a++) this.addColor(5,a,5);
	}

	this.getColorTable = function () {
		var colors = this.colorArray;
		var tableCode = '';
		tableCode += '<table border="0" cellspacing="0" cellpadding="1">';
		for (i=0, n=colors.length; i < n; i++) {
		  if (i % this.perline == 0) { tableCode += '<tr>'; }
		  tableCode += '<td bgcolor="#000000"><a style="outline: 1px solid ' + colors[i] + '; color: '
			  + colors[i] + '; background: ' + colors[i] + ';font-size: 9px;" title="'
			  + colors[i] + '" href="javascript:'+this.varname+'.setColor(\'' + colors[i] + '\');">&nbsp;&nbsp;&nbsp;</a></td>';
		  if (i % this.perline == this.perline - 1) { tableCode += '</tr>'; }
		}
		if (i % this.perline != 0) { tableCode += '</tr>'; }
		tableCode += '</table>';
		return tableCode;
	}

	this.relateColor = function (color) {
		if (color == '') {
			this.swatch.style.background = this.nocolor;
			this.swatch.style.color = this.nocolor;
			color = this.nocolor;
		} else {
			this.swatch.style.background = color;
			this.swatch.style.color = color;
		}
		//alert(eval(this.field.title));
	}

	this.hideShowCovered = function () {
		if (!this.ie) {
			return;
		}
		var tags = new Array("applet", "iframe", "select");
		var el = this.picker;

		var p = this.getAbsolutePos(el);
		var EX1 = p.x;
		var EX2 = el.offsetWidth + EX1;
		var EY1 = p.y;
		var EY2 = el.offsetHeight + EY1;
		//alert(EX1+','+EX2+','+EY1+','+EY2);

		for (var k = tags.length; k > 0; ) {
			var ar = document.getElementsByTagName(tags[--k]);
			var cc = null;

			for (var i = ar.length; i > 0;) {
				cc = ar[--i];

				p = this.getAbsolutePos(cc);//alert(p.x+','+p.y);
				var CX1 = p.x;
				var CX2 = cc.offsetWidth + CX1;
				var CY1 = p.y;
				var CY2 = cc.offsetHeight + CY1;
				//alert(cc.name+ '::'+CX1+','+CX2+','+CY1+','+CY2);

				if (this.picker.style.display == 'none' || (CX1 > EX2) || (CX2 < EX1) || (CY1 > EY2) || (CY2 < EY1)) {
					cc.style.visibility = "visible";
				} else {
					cc.style.visibility = "hidden";
				}
			}
		}
	}

// properties
	this.ie = document.all; // true if ie
	this.perline = 9;
	this.curId = id;
	this.varname = varname;

	this.swatch = this.getObj(id);
	this.field = this.getObj(id + 'field');
	this.pickername = id + 'colorpicker';

	this.colorLevels = Array('0', '3', '6', '9', 'C', 'F');
	this.colorArray = Array();
	this.nocolor = this.ie ? '' : 'none';

	this.relateColor(color);
}
