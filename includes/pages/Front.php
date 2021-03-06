<?php

class Front extends Page {

	protected function render ( ) {
		$list = '<table>';
		foreach ( $this->database->getSortedMeetings() as $date => $meeting ) {
			$renderSelf = null;
			$uniques = 0;
			$nonuniques = 0;
			$start = null;
			foreach ( $this->sortSchedule($meeting->schedule) as $item ) {
				if ( is_null($start) )
					$start = $this->showTime($item->start);
				if ( $item->unique ) {
					$uniques++;
					if ( is_null($renderSelf) )
						$renderSelf = false;
				} else {
					$nonuniques++;
					if ( is_null($renderSelf) )
						$renderSelf = true;
				}
			}
			$list .= '<tr><td rowspan="'.($uniques+1).'">'.$this->weekDay($date, true).(is_numeric(@$meeting->days)?'<br />&mdash;<br />'.$this->weekDay($this->getEndDate($date, $meeting->days), true):'').$this->loggedInUserInDate($date).'</td>
				<td class="date" rowspan="'.($uniques+1).'">'.$this->readableDate($date).(is_numeric(@$meeting->days)?'<br />&mdash;<br />'.$this->readableDate($this->getEndDate($date, $meeting->days)):'').'</td>
				<td'.($uniques > 0?' class="title"':'').'>'.($nonuniques > 0?'<a href="?meeting='.$date.'">'.$meeting->{'title'}.'</a>':$meeting->title).'</td>
				<td>'.$start."</td></tr>\n";
			foreach ( $meeting->schedule as $id => $item ) {
				if ( $item->unique ) {
					$list .= '<tr><td><a href="?meeting='.$date.'&amp;subid='.(isset($item->id)?$item->id:$id).'">'.$item->{'title'}.'</a></td><td>'.$item->start."</tr>\n";
				}
			}
		}
		$list .= '</table>';
		$this->content = '<h1>DIKUrevy-møder</h1>';
		$this->content .= $list;
		if ( $this->auth->loggedIn() )
			$this->content .= '<p>* = dage du har indbrettet omkring.</p>';
		//$this->content .= '<iframe align="middle" style="border-width: 0" scrolling="no" frameborder="0" width="800" height="600" src="//webmail.one.com/calendar/embed.html#src=http%3A%2F%2Fmoeder.dikurevy.dk%2F%3Fdo%3Dical%26tags%3Dintro&amp;src=http%3A%2F%2Fmoeder.dikurevy.dk%2F%3Fdo%3Dical%26tags%3Drevy&amp;name=DIKUrevy%20Intro%20m%C3%B8der&amp;name=DIKUrevy%20m%C3%B8der&amp;color=%2326807e&amp;color=%232c8026&amp;navigation=true&amp;date=true&amp;tabs=true&amp;view=month&amp;weekStart=1&amp;locale=da&amp;tz=Europe%2FCopenhagen&amp;title=Kalender%20for%20DIKUrevyens%20aktiviteter"></iframe>';
	}

	private function loggedInUserInDate ( $date ) {
		if ( !$this->auth->loggedIn() )
			return '';
		if ( isset($this->database->getMeeting($date)->users->{$this->auth->userinfo->identity}) )
			return '*';
		return '';
	}
}
