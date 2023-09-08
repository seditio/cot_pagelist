<!-- BEGIN: MAIN -->
<ul class="list-unstyled">
<!-- BEGIN: PAGE_ROW -->
	<li class="{PAGE_ROW_ODDEVEN}">
		<a href={PAGE_ROW_URL} class="d-block">{PAGE_ROW_NUM}. {PAGE_ROW_SHORTTITLE}</a>
		<p class="small mb-0">{PAGE_ROW_DATE}</p>
		<p class="small mb-0">Comments: {PAGE_ROW_COMMENTS_COUNT}</p>
	</li>
<!-- END: PAGE_ROW -->
</ul>

<!-- IF {PAGE_TOP_PAGINATION} -->
<nav aria-label="Pagelist Pagination">
	<ul class="pagination pagination-sm justify-content-center">
		{PAGE_TOP_PAGEPREV}{PAGE_TOP_PAGINATION}{PAGE_TOP_PAGENEXT}
	</ul>
</nav>
<!-- ENDIF -->
<!-- END: MAIN -->
