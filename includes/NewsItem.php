<?php namespace RAL;
class NewsItem {
	public $Id;
	public $Created;
	public $Author;
	public $Email;
	public $Title;
	public $Content;

	public $Parent;

	public function __construct($row, $parent) {
		$this->Id = $row['Id'];
		$this->Created = $row['Created'];
		$this->Author = $row['Author'];
		$this->Email = $row['Email'];
		$this->Title = $row['Title'];
		$this->Content = $row['Content'];

		$this->Parent = $parent;
	}
	public function draw() {
		$id = $this->Id;
		$created = date('M j, Y', strtotime($this->Created));
		$author = $this->Author;
		$email = $this->Email;
		$title = $this->Title;
		$content = $this->getContentAsHtml();

		print <<<HTML
		<article class=news-item>
		<nav>
			<h3 class=title>$title</h3>
			by <a href="mailto:$email">$author</a>
		/ <time>$created</time>
		</nav>
		$content
		</article>
HTML;
	}
	public function drawSelection($selection) {
		print <<<HTML
		<article>
		<h2>News</h2>

HTML;
		foreach ($selection as $item) {
			$item->draw();
		}
		print <<<HTML
		</article>

HTML;
	}
	public function getContentAsHtml() {
		$bbparser = $GLOBALS['RM']->getbbparser();
		$visitor = $GLOBALS['RM']->getLineBreakVisitor();
		$bbparser->parse(htmlentities($this->Content));
		$bbparser->accept($visitor);
		return $bbparser->getAsHtml();
	}
}
