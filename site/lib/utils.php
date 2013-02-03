<?php

function dateFields($dateTime) {
  return array("day" => $dateTime->format('d'),
	       "month" => $dateTime->format('n'),
	       "year" => $dateTime->format('Y'));
}

function toDateTime($strDate) {
  if ($strDate == NULL)
    return NULL;
  else
    return new DateTime($strDate);
}

function formatDate ($dateTime)
{
  if ($dateTime==NULL) return "";
  if ($dateTime->format('H')==0 && $dateTime->format('i')==0)
    return $dateTime->format('Y-m-d');
  else
    return $dateTime->format('Y-m-d H:i');
}

function formatRange (&$start, &$end)
{
  // if any is null return default
  if ($start==NULL || $end==NULL) {
    return formatDate($start) . " -- " . formatDate($end);
  }

  if ($start->format("H:i:s")=="00:00:00" &&
      $end->format("H:i:s")=="00:00:00") {
    
    // full days, so last day (month, year) is exclusive - recalculate end
    $end2 = new DateTime();
    $end2 = $end;
    $startF = dateFields($start);
    $endF = dateFields($end);
    if ($startF['day']==1 && $endF['day']==1) {
      // at least month period
      if ($startF['month']==1 && $endF['month']==1) {
	// year period!
	$end2->sub(new DateInterval("P1Y"));
	$format = 'Y';
      } else {
	// month period!
	$end2->sub(new DateInterval("P1M"));
	$format = 'Y-m';
      }
    } else {
      // day period!
      $end2->sub(new DateInterval("P1D"));
      $format = 'Y-m-d';
    }

    // now format
    if ($start == $end2) {
      // just one day (month, year)
      return $start->format($format);
    } else {
      // range
      return $start->format($format) . " -- " . $end2->format($format);
    }
  }

  // default - full
  return formatDate($start) . " -- " . formatDate($end);
}

function javascriptDate($d) {
  return "new Date(".$d->format('Y').",".
    ($d->format('n')-1).",".
    $d->format('d').",".
    $d->format('H').",".
    $d->format('i').",".
    $d->format('s').")";
}

?>