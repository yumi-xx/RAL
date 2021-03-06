<?php namespace RAL;
$dir = dirname(__FILE__);
if (is_dir("{$dir}/jBBCode")) {
	include "{$dir}/jBBCode/Parser.php";
	include "{$dir}/LineBreakVisitor.php";
	include "{$dir}/SmileyVisitor.php";
} if (is_dir("{$dir}/b8")) {
	include "{$dir}/b8/b8.php";
}

class ResourceManager {
	public $raldb;
	public $bbparser;
	public $linebreakvisitor;
	public $smileyvisitor;
	public $b8;

	function asHtml($content) {
		$bbparser = $this->getbbparser();
		$visitors = [
			$this->getLineBreakVisitor(),
			$this->getSmileyVisitor() ];
		$bbparser->parse(htmlentities($content));
		foreach ($visitors as $v) {
			$bbparser->accept($v); }
		return $bbparser->getAsHtml();
	}

	public function asText($content) {
		$bbparser = $this->getbbparser();
		$bbparser->parse($content);
		return $bbparser->getAsText();
	}

	function getb8() {
		if ($this->b8) return $this->b8;
		$b8 = new \b8(["storage" => "dba"
			], ["database" => CONFIG_SPAM_DB
			], ["get_html" => true
			], ["multibyte" => true]);
		$this->b8 = $b8;
		return $this->b8;
	}
	function getdb() {
		if ($this->raldb) return $this->raldb;
		$dbh = mysqli_connect(
			CONFIG_RAL_SERVER,
			CONFIG_RAL_USERNAME,
			CONFIG_RAL_PASSWORD,
			CONFIG_RAL_DATABASE
		);
		mysqli_set_charset($dbh, 'utf8');
		$this->raldb = $dbh;
		return $this->raldb;
	}
	function getbbparser() {

		if ($this->bbparser) return $this->bbparser;

		$bbparser = new \jBBCode\Parser();
	        $urlValidator = new \JBBCode\validators\UrlValidator();

		/* [b] bold tag */
	        $builder = new \jBBCode\CodeDefinitionBuilder(
			'b',
			'<strong>{param}</strong>'
		);
		$bbparser->addCodeDefinition($builder->build());

		/* [i] emphasis tag */
	        $builder = new \jBBCode\CodeDefinitionBuilder(
			'i',
			'<em>{param}</em>'
		);
		$bbparser->addCodeDefinition($builder->build());

		/* [url] link tag */
		$builder = new \jBBCode\CodeDefinitionBuilder(
			'url',
			'<a href="{param}">{param}</a>'
		);
		$builder->setParseContent(false)->setBodyValidator(
			$urlValidator);
		$bbparser->addCodeDefinition($builder->build());

		/* [url=http://example.com] link tag */
		$builder = new \jBBCode\CodeDefinitionBuilder(
			'url',
			'<a href="{option}">{param}</a>'
		);
		$builder->setUseOption(true)->setParseContent(
			true)->setOptionValidator($urlValidator);
		$bbparser->addCodeDefinition($builder->build());

	        /* [color] color tag */
		$builder = new \jBBCode\CodeDefinitionBuilder(
			'color',
			'<span style="color: {option}">{param}</span>'
		);
		$builder->setUseOption(true)->setOptionValidator(
			new \JBBCode\validators\CssColorValidator());
		$bbparser->addCodeDefinition($builder->build());

		/* [spoiler] hidden tag */
	        $builder = new \jBBCode\CodeDefinitionBuilder(
			'spoiler',
			'<span class=spoiler>{param}</span>'
		);
		$bbparser->addCodeDefinition($builder->build());

		/* [aa] ASCII Art tag */
		$builder = new \jBBCode\CodeDefinitionBuilder(
			'aa',
			'<samp class=aa>{param}</samp>'
		);
		$bbparser->addCodeDefinition($builder->build());
		/* [sjis] ASCII Art tag */
		$builder = new \jBBCode\CodeDefinitionBuilder(
			'sjis',
			'<samp class=sjis>{param}</samp>'
		);
		$builder->setParseContent(false);
		$bbparser->addCodeDefinition($builder->build());

		/* [quote] Quote Art tag */
		$figure = <<<HTML
<figure>
	<blockquote>{param}</blockquote>
	<figcaption>— {option}</figcaption>
</figure>

HTML;
		$builder = new \jBBCode\CodeDefinitionBuilder(
			'quote',
			$figure
		);
		$builder->setUseOption(true);
		$bbparser->addCodeDefinition($builder->build());

		$this->bbparser = $bbparser;
		return $this->bbparser;
	}
	function getLineBreakVisitor() {
		if ($this->linebreakvisitor) return $this->linebreakvisitor;
		$this->linebreakvisitor =  new \JBBCode\Visitors\LineBreakVisitor();
		return $this->linebreakvisitor;
	}
	function getSmileyVisitor() {
		if ($this->smileyvisitor) return $this->smileyvisitor;
		$this->smileyvisitor =  new \JBBCode\Visitors\SmileyVisitor();
		return $this->smileyvisitor;
	}
}
