<!-- BEGIN: MAIN -->
<ul class="list-unstyled comments">
<!-- BEGIN: PAGE_ROW -->
	<li class="{PAGE_ROW_ODDEVEN} px-3 py-2">
		<figure class="me-3 mb-1 float-start">
			{PAGE_ROW_USER_AVATAR}
		</figure>
		<a href={PAGE_ROW_URL} class="fw-bold d-block">{PAGE_ROW_SHORTTITLE}</a>
		<div class="text small lh-sm mb-2">
			{PAGE_ROW_TEXT_PLAIN|cot_cutstring($this, '160')}
		</div>
		<p class="text-end small mb-0">
			{PAGE_ROW_USER_NAME} @ {PAGE_ROW_DATE}
		</p>
	</li>
<!-- END: PAGE_ROW -->
</ul>

<!-- IF {PAGE_TOP_PAGINATION} -->
<nav aria-label="Pagelist Pagination">
	<ul class="pagination pagination-sm justify-content-center mb-0">
		{PAGE_TOP_PAGEPREV}{PAGE_TOP_PAGINATION}{PAGE_TOP_PAGENEXT}
	</ul>
</nav>
<!-- ENDIF -->
<!-- END: MAIN -->
