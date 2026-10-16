<h1>Category "{$category->getTitle()->getValue()}"</h1>
<blockquote>{$category->getDescription()->getValue()}</blockquote>

<h2>Articles</h2>
{foreach $articlesLatest as $articleLatest}
    <div class="article">
        <div class="article__link"><a
                    href="{$urlGenerator->generate('article__show',['id' => $articleLatest->getId()])->toString()}">&#x2197;</a>
        </div>
        <h3>{$articleLatest->getTitle()->getValue()}</h3>
        <img src="{$articleLatest->getPicUrl()->toString()}" alt="article picture">
        <article>{$articleLatest->getDescription()->getValue()}</article>
    </div>
{/foreach}