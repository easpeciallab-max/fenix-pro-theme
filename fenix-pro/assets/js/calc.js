/**
 * เครื่องคำนวณขนาดล็อต
 *
 * สูตรที่ใช้ (บัญชีสกุลเดียวกับสกุลที่สินทรัพย์จบท้าย เช่น บัญชี USD กับ XAUUSD)
 *   เงินที่ยอมเสี่ยง = ทุน x ความเสี่ยง%
 *   ขาดทุนต่อ 1 ล็อต = ขนาดสัญญา x ระยะ Stop Loss
 *   ล็อต = เงินที่ยอมเสี่ยง / ขาดทุนต่อ 1 ล็อต   (ปัดลงตามขั้นล็อต)
 *   มาร์จิ้น = ล็อต x ขนาดสัญญา x ราคา / เลเวอเรจ
 *
 * ไม่ส่งข้อมูลออกนอกเบราว์เซอร์
 */
( function () {
	'use strict';

	var form = document.getElementById( 'fx-calc' );
	var out  = document.getElementById( 'fx-calc-out' );
	if ( ! form || ! out ) {
		return;
	}

	var el = {
		symbol:   form.querySelector( '#cf-symbol' ),
		contract: form.querySelector( '#cf-contract' ),
		balance:  form.querySelector( '#cf-balance' ),
		risk:     form.querySelector( '#cf-risk' ),
		sl:       form.querySelector( '#cf-sl' ),
		price:    form.querySelector( '#cf-price' ),
		leverage: form.querySelector( '#cf-leverage' ),
		minlot:   form.querySelector( '#cf-minlot' )
	};

	var slot = {};
	Array.prototype.forEach.call( out.querySelectorAll( '[data-out]' ), function ( node ) {
		slot[ node.getAttribute( 'data-out' ) ] = node;
	} );

	var money = new Intl.NumberFormat( 'th-TH', { minimumFractionDigits: 2, maximumFractionDigits: 2 } );
	var lotFm = new Intl.NumberFormat( 'th-TH', { minimumFractionDigits: 2, maximumFractionDigits: 4 } );

	function num( node ) {
		var v = parseFloat( node && node.value );
		return isFinite( v ) ? v : 0;
	}

	/* ปัดลงให้ลงตัวกับขั้นล็อตของโบรก */
	function floorStep( value, step ) {
		if ( ! ( step > 0 ) ) {
			return value;
		}
		var n = Math.floor( value / step + 1e-9 ) * step;
		return parseFloat( n.toFixed( 6 ) );
	}

	function show( key, text ) {
		if ( slot[ key ] ) {
			slot[ key ].textContent = text;
		}
	}

	function warn( text ) {
		if ( ! slot.warn ) {
			return;
		}
		if ( text ) {
			slot.warn.textContent = text;
			slot.warn.hidden = false;
		} else {
			slot.warn.textContent = '';
			slot.warn.hidden = true;
		}
	}

	function calc() {
		var contract = num( el.contract );
		var balance  = num( el.balance );
		var riskPct  = num( el.risk );
		var sl       = num( el.sl );
		var price    = num( el.price );
		var leverage = num( el.leverage );
		var minlot   = num( el.minlot );

		if ( ! ( contract > 0 ) || ! ( balance > 0 ) || ! ( riskPct > 0 ) || ! ( sl > 0 ) ) {
			show( 'lots', '—' );
			show( 'risk', '—' );
			show( 'loss', '—' );
			show( 'perpoint', '—' );
			show( 'margin', '—' );
			show( 'marginpct', '—' );
			warn( 'กรอกขนาดสัญญา ทุน ความเสี่ยง และระยะ Stop Loss ให้ครบก่อน' );
			return;
		}

		var riskMoney  = balance * riskPct / 100;
		var lossPerLot = contract * sl;
		var rawLots    = riskMoney / lossPerLot;
		var lots       = floorStep( rawLots, minlot > 0 ? minlot : 0.01 );
		var realLoss   = lots * lossPerLot;
		var perPoint   = lots * contract;
		var margin     = leverage > 0 && price > 0 ? lots * contract * price / leverage : 0;
		var marginPct  = balance > 0 ? margin / balance * 100 : 0;

		show( 'lots', lots > 0 ? lotFm.format( lots ) + ' ล็อต' : 'ต่ำกว่าล็อตขั้นต่ำ' );
		show( 'risk', money.format( riskMoney ) );
		show( 'loss', lots > 0 ? money.format( realLoss ) : '—' );
		show( 'perpoint', lots > 0 ? money.format( perPoint ) + ' ต่อ 1.00' : '—' );
		show( 'margin', lots > 0 ? money.format( margin ) : '—' );
		show( 'marginpct', lots > 0 ? money.format( marginPct ) + '%' : '—' );

		if ( slot.lotsnote ) {
			slot.lotsnote.textContent = lots > 0
				? 'ก่อนปัดคือ ' + lotFm.format( rawLots ) + ' ล็อต ปัดลงตามขั้น ' + lotFm.format( minlot ) + ' แล้ว'
				: 'ลดความเสี่ยงต่อไม้ลง หรือเพิ่มทุน หรือหาบัญชีที่ล็อตขั้นต่ำเล็กกว่านี้';
		}

		if ( lots <= 0 ) {
			warn( 'ด้วยทุนและระยะ Stop Loss ชุดนี้ ขนาดไม้ที่ความเสี่ยงยอมให้ยังเล็กกว่าล็อตขั้นต่ำของโบรก ถ้าฝืนเปิดเท่าขั้นต่ำ ความเสี่ยงต่อไม้จะสูงกว่าที่ตั้งไว้' );
		} else if ( marginPct >= 50 ) {
			warn( 'มาร์จิ้นที่ถูกล็อกเกินครึ่งหนึ่งของทุนแล้วตั้งแต่ไม้เดียว ถ้าระบบเปิดหลายไม้ ระดับมาร์จิ้นจะร่วงเร็วมาก' );
		} else if ( riskPct > 5 ) {
			warn( 'ความเสี่ยงต่อไม้เกิน 5% ของทุน เป็นระดับที่ขาดทุนติดกันไม่กี่ไม้ก็กระทบพอร์ตหนัก' );
		} else {
			warn( '' );
		}
	}

	/* เปลี่ยนสินทรัพย์แล้วเติมค่าเริ่มต้นของสินทรัพย์นั้นให้
	   ระยะ Stop Loss ของแต่ละสินทรัพย์อยู่คนละสเกล ถ้าไม่เปลี่ยนตามจะได้ผลที่อ่านผิด */
	var slHint = document.getElementById( 'cf-sl-hint' );

	el.symbol.addEventListener( 'change', function () {
		var opt = el.symbol.options[ el.symbol.selectedIndex ];
		var val = opt.value;
		if ( 'custom' !== val ) {
			el.contract.value = val;
		}
		if ( opt.getAttribute( 'data-price' ) ) {
			el.price.value = opt.getAttribute( 'data-price' );
		}
		if ( opt.getAttribute( 'data-sl' ) ) {
			el.sl.value = opt.getAttribute( 'data-sl' );
		}
		if ( slHint && opt.getAttribute( 'data-unit' ) ) {
			slHint.textContent = opt.getAttribute( 'data-unit' );
		}
		calc();
	} );

	form.addEventListener( 'input', calc );
	form.addEventListener( 'reset', function () {
		window.setTimeout( calc, 0 );
	} );

	calc();
}() );
