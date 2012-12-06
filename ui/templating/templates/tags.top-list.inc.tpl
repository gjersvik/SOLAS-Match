{if isset($top_tags) AND is_array($top_tags) AND count($top_tags) > 0}
	<h3><i class="icon-tags"></i> Popular tags</h3>
	<ul class="nav nav-list unstyled">
		{foreach $top_tags as $tag}
			<li>
                <div class="tag">
                    {assign var="tag_label" value=$tag->getLabel()}
				    <a href="{urlFor name="tag-details" options="label.$tag_label"}" class="label">{$tag_label}</a>
                </div>
			</li>
		{/foreach}
	</ul>
        <p style="margin-bottom:20px;"></p>        
{/if}