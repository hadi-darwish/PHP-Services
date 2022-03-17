<?php
	use RawadyMario\Constants\Code;
	use RawadyMario\Constants\DateFormats;
	use RawadyMario\Constants\HttpCode;
	use RawadyMario\Constants\Lang;
	use RawadyMario\Constants\Status;
	use RawadyMario\Models\CurrencyPosition;

	include_once 'vendor/autoload.php';

	$arr = [
		Code::class => "",Code::SUCCESS,
		DateFormats::class => DateFormats::DATETIME_FORMAT_SAVE,
		HttpCode::class => HttpCode::OK,
		Lang::class => Lang::ALL,
		Status::class => Status::SUCCESS,
		CurrencyPosition::class => CurrencyPosition::POST,
	];

	foreach ($arr AS $i => $elem) {
		echo $i;
		echo "<br />";
		echo $elem;
		echo "<hr />";
	}