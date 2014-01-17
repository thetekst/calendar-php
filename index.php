<?
function pc_calendar($month,$year,$opts = '') {
	
	// установка опций по умолчанию
	if (! is_array($opts)) { $opts = array(); }
	if (! isset($opts['today_color'])) { $opts['today_color'] = '#FFFF00'; }
	
	if (! isset($opts['month_link'])) {
		$opts['month_link'] = '<a href="'.$_SERVER['PHP_SELF'].'?month=%d&year=%d">%s</a>';
	}
	
	list($this_month,$this_year,$this_day) = explode(',',strftime('%m,%Y,%d'));
	$day_highlight = (($this_month == $month) && ($this_year == $year));
	
	list($prev_month,$prev_year) = explode(',',strftime('%m,%Y',mktime(0,0,0,$month - 1,1,$year)));
	$prev_month_link = sprintf($opts['month_link'],	$prev_month,$prev_year,'&lt;');
	
	list($next_month,$next_year) =	explode(',',strftime('%m,%Y',mktime(0,0,0,$month+1,1,$year)));
	$next_month_link = sprintf($opts['month_link'],	$next_month,$next_year,'&gt;');
	
	$monthRu = array(
		'January' 	=> 'Январь',
		'February' 	=> 'Февраль',
		'March' 	=> 'Март',
		'April' 	=> 'Апрель',
		'May' 		=> 'Май',
		'June' 		=> 'Июнь',
		'July' 		=> 'Июль',
		'August' 	=> 'Август',
		'September' => 'Сентябрь',
		'October' 	=> 'Октябрь',
		'November' 	=> 'Ноябрь',
		'December' 	=> 'Декабрь'
	);
	?>
	
	<table border="0" cellspacing="0" cellpadding="2" align="center">
		<tr>
			<td align="left">
				<?php print $prev_month_link ?>
			</td>
			<td colspan="5" align="center"><?php
				$timeForm =  strftime('%B %Y',mktime(0,0,0,$month,1,$year));
				//translate month on RU
				foreach($monthRu as $key => $m) {
					if(strpos($timeForm, $key) !== false) {
						echo str_replace($key, $m, $timeForm);
					}
				}
				?></td>
			<td align="right">
				<?php print $next_month_link ?>
			</td>
		</tr>
	
	<?php
	$totaldays = date('t',mktime(0,0,0,$month,1,$year));
	
	// выводим дни недели
	print '<tr>';
	$weekdays = array('Пн','Вт','Ср','Чт','Пт','Сб','Вс');
	//$weekdays = array('Su','Mo','Tu','We','Th','Fr','Sa');
	
	while (list($k,$v) = each($weekdays)) {
		print '<td align="center">'.$v.'</td>';
	}
	
	print '</tr><tr>';
	
	// выравниваем первый день месяца по соответствующему дню недели
	$day_offset = date("w",mktime(0, 0, 0, $month, 0, $year)); // понедельник
	//$day_offset = date("w",mktime(0, 0, 0, $month, 1, $year)); // воскресенье
	
	if ($day_offset > 0) {
		for ($i = 0; $i < $day_offset; $i++) { print '<td>&nbsp;</td>'; }
	}
	
	$yesterday = time() - 86400;
	
	// выводим дни
	for ($day = 1; $day <= $totaldays; $day++) {
		$day_secs = mktime(0,0,0,$month,$day,$year);
		
		if ($day_secs >= $yesterday) {
			if ($day_highlight && ($day == $this_day)) {
				print sprintf('<td align="center" bgcolor="%s">%d</td>',
				$opts['today_color'],$day);
			} else {
				print sprintf('<td align="center">%d</td>',$day);
			}
		} else {
			print sprintf('<td align="center">%d</td>',$day);
		}
		$day_offset++;
		
		// начинаем новую строку каждую неделю
		if ($day_offset == 7) {
			$day_offset = 0;
			print "</tr>\n";
			if ($day < $totaldays) { print '<tr>'; }
		}
	}
	
	// заполнение последней недели пробелами
	if ($day_offset > 0) { $day_offset = 7 - $day_offset; }
	if ($day_offset > 0) {
		for ($i = 0; $i < $day_offset; $i++) { print '<td>&nbsp;</td>'; }
	}
	print '	</tr>
			<tr>
				<td colspan="7"><a href="'.$_SERVER['PHP_SELF'].'">Текущяя дата</a></td>
			</tr>
			</table>';
}

// Front-end
setlocale(LC_ALL, 'ru-RU');
date_default_timezone_set('Europe/Moscow');

// печать календаря для текущего месяца
if(empty($_GET['month']) && empty($_GET['year'])) {
	list($month,$year) = explode(',',date('m,Y'));
	pc_calendar($month,$year);
} else {
	pc_calendar($_GET['month'],$_GET['year']);
}
?>