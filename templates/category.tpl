<h1>Category "{$category->getTitle()->getValue()}"</h1>
<blockquote>{$category->getDescription()->getValue()}</blockquote>

<ul>Articles</ul>
{foreach $articlesLatest as $articleLatest}
    <h3>{$articleLatest->getTitle()->getValue()}</h3>
    <img src="{$articleLatest->getPicUrl()->toString()}" alt="article picture">
    <article>{$articleLatest->getDescription()->getValue()}</article>
{/foreach}