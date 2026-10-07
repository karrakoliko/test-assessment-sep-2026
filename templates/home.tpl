<h1>Blog</h1>

{foreach $homePageCategories as $homePageCategory}
    <div>{$homePageCategory->getCategory()->getTitle()->getValue()}</div>
    <ul>
        {foreach $homePageCategory->getLatestArticles() as $homePageArticle}
            <li>{$homePageArticle->getTitle()->getValue()}</li>
        {/foreach}

    </ul>
{/foreach}