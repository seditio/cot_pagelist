<!-- BEGIN: MAIN -->
<ul class="list-unstyled">
<!-- BEGIN: PAGE_ROW -->
	<li class="{PAGE_ROW_ODDEVEN}">
		<a href={PAGE_ROW_URL} class="d-block">{PAGE_ROW_NUM}. {PAGE_ROW_SHORTTITLE}</a>
		<p class="small mb-0">{PAGE_ROW_DATE}</p>
		<p class="small mb-0">Comments: {PAGE_ROW_COMMENTS_COUNT}</p>
	</li>
<!-- END: PAGE_ROW -->
<!-- BEGIN: NO_ROW -->
	<li>
		{PHP.L.None}
	</li>
<!-- END: NO_ROW -->
</ul>
<!-- IF {PAGINATION} -->
<nav aria-label="Pagelist Pagination">
	<ul class="pagination pagination-sm justify-content-center mb-0">
		{PREVIOUS_PAGE}{PAGINATION}{NEXT_PAGE}
	</ul>
</nav>
<!-- ENDIF -->
<!-- END: MAIN -->
