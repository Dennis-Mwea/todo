interface Pagination<T> {
	data: Array<T>
	to: number
	from: number
	path: String,
	total: number
	per_page: number
	last_page: number
	current_page: number
	last_page_url: String,
	next_page_url: String,
	first_page_url: String,
}