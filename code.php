<?php


function findMonday($time)
{
	if (date('D', $time) != 'Mon')
	{
		return strtotime('last Monday', $time);
	}
	return $time;
}

if (!isset($_GET['start']))
{
    $current_week = date('W');
    $current_timestamp = time();
} else {
    $current_week = date('W', strtotime($_GET['start']));
    $current_timestamp = strtotime($_GET['start']);
}

$end_timestamp = strtotime((date('Y', $current_timestamp)+1).'-01-01');

$counter = 0;

for ($week_i = $current_week; $week_i <= 53; $week_i++)
{
    $counter++;
    // start with the "fuzzy" current time
    $base_ts = strtotime('+'.($week_i - $current_week).' weeks', $current_timestamp);

    $monday = findMonday($base_ts);
    if ($monday < strtotime((date('Y', $current_timestamp)+1).'-01-01'))
        print_week_block($base_ts);
    if ($counter % 4 == 0)
        echo '<div style="page-break-after: always;">&nbsp;</div>';
}


function print_week_block($week_timestamp) {

    // More weekday tapes? Add them here!
    $weekdays = array('A', 'B');
    $weekdays_count = count($weekdays);

    // More friday tapes? Add them here!
    $fridays = array(1, 2, 3, 4, 5);
    $fridays_count = count($fridays);

	// Now we will know monday.
	$monday_ts = findMonday($week_timestamp);

	// Just because I'm paranoid.
	$week = date('W', $monday_ts);

	// A or B tapes?
	$weekday_delta = $week % $weekdays_count;
        $weekday_tape = $weekdays[$weekday_delta];

	// Which firday tape?
	$friday_delta = $week % $fridays_count;
	$friday_tape = $fridays[$friday_delta];

	// Do we need to override with the monthly tape?
	$friday_ts = strtotime('next Friday', $monday_ts);
	$next_friday_month = date('m', strtotime('next Friday', $friday_ts));
	if (date('m', $friday_ts) != $next_friday_month)
		$friday_tape = null;


?>
<div style="page-break-inside: avoid;">

<table width="600" style="margin-left: 50px;">
	<caption>Backup Tape Schedule - Week <?php echo $week; ?> - <?php echo date('m/d/Y', $monday_ts); ?> to <?php echo date('m/d/Y', $friday_ts); ?></caption>
	<thead>
		<tr>
			<th>Date</th>
			<th colspan="<?php echo $weekdays_count; ?>">Monday</th>
			<th colspan="<?php echo $weekdays_count; ?>">Tuesday</th>
			<th colspan="<?php echo $weekdays_count; ?>">Wednesday</th>
			<th colspan="<?php echo $weekdays_count; ?>">Thursday</th>
			<th colspan="<?php echo $fridays_count + 1; ?>">Friday</th>
		</tr>
	</thead>
	<tbody>
<?php for($i = 1; $i < 5; $i++): ?>
		<tr<?php if ($i % 2 == 0): ?> class="odd"<?php endif; ?>>
			<td><?php echo date('D, F j', strtotime('+'.($i - 1).' day', $monday_ts)); ?></td>
<?php for($a = 1; $a < $i; $a++): ?>
<?php foreach($weekdays as $tape): ?>
			<td><span><?php echo $tape; ?></span></td>
<?php endforeach; ?>
<?php endfor; ?>
<?php foreach($weekdays as $tape): ?>
			<td><span<?php if ($weekday_tape == $tape): ?> class="should_use"<?php endif; ?>><?php echo $tape; ?></span></td>
<?php endforeach; ?>
<?php for($a = $i + 1; $a < 5; $a++): ?>
<?php foreach($weekdays as $tape): ?>
			<td><span><?php echo $tape; ?></span></td>
<?php endforeach; ?>
<?php endfor; ?>
<?php foreach($fridays as $tape): ?>
			<td><span><?php echo $tape; ?></span></td>
<?php endforeach; ?>
			<td><span><?php echo date('F', $friday_ts); ?></span></td>
		</tr>
<?php endfor; ?>
		<tr>
			<td><?php echo date('D, F j', strtotime('+'.($i - 1).' day', $monday_ts)); ?></td>
<?php for($a = 1; $a < 5; $a++): ?>
<?php foreach($weekdays as $tape): ?>
			<td><span><?php echo $tape; ?></span></td>
<?php endforeach; ?>
<?php endfor; ?>
<?php for($i = 1; $i < 6; $i++): ?>
			<td><span<?php if ($i == $friday_tape): ?> class="should_use"<?php endif; ?>><?php echo $i; ?></span></td>
<?php endfor; ?>
			<td><span<?php if ($friday_tape == null): ?> class="should_use"<?php endif; ?>><?php echo date('F', $friday_ts); ?></span></td>
		</tr>
	</tbody>
</table>
<br />
</div>

<?php
}