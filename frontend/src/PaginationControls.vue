<template>
    <nav v-if="shouldShowPagination" class="mt-4">
        <ul class="pagination justify-content-center flex-wrap">
            <li class="page-item" :class="{ disabled: currentPage === 1 }">
                <a class="page-link" href="#" @click.prevent="goToPage(currentPage - 1)">
                    &laquo; Previous
                </a>
            </li>

            <!-- First page -->
            <li v-if="showFirstPage" class="page-item">
                <a class="page-link" href="#" @click.prevent="goToPage(1)">1</a>
            </li>

            <!-- First ellipsis -->
            <li v-if="showFirstEllipsis" class="page-item disabled">
                <span class="page-link">...</span>
            </li>

            <!-- Page numbers -->
            <li 
                v-for="page in visiblePages" 
                :key="page" 
                class="page-item" 
                :class="{ active: page === currentPage }"
            >
                <a class="page-link" href="#" @click.prevent="goToPage(page)">
                    {{ page }}
                </a>
            </li>

            <!-- Last ellipsis -->
            <li v-if="showLastEllipsis" class="page-item disabled">
                <span class="page-link">...</span>
            </li>

            <!-- Last page -->
            <li v-if="showLastPage" class="page-item">
                <a class="page-link" href="#" @click.prevent="goToPage(totalPages)">
                    {{ totalPages }}
                </a>
            </li>

            <li class="page-item" :class="{ disabled: currentPage === totalPages }">
                <a class="page-link" href="#" @click.prevent="goToPage(currentPage + 1)">
                    Next &raquo;
                </a>
            </li>
        </ul>
    </nav>
</template>

<script>
export default {
    name: 'PaginationControls',
    props: {
        currentPage: {
            type: Number,
            required: true,
            default: 1,
            validator: value => value >= 1
        },
        totalPages: {
            type: Number,
            required: true,
            default: 1,
            validator: value => value >= 1
        },
        maxVisiblePages: {
            type: Number,
            default: 5,
            validator: value => value >= 3
        }
    },
    computed: {
        shouldShowPagination() {
            return this.totalPages > 1
        },
        showFirstPage() {
            return this.paginationStart > 1
        },
        showFirstEllipsis() {
            return this.paginationStart > 2
        },
        showLastPage() {
            return this.paginationEnd < this.totalPages
        },
        showLastEllipsis() {
            return this.paginationEnd < this.totalPages - 1
        },
        paginationStart() {
            // Calculate starting page number
            const half = Math.floor(this.maxVisiblePages / 2)
            let start = this.currentPage - half
            
            // Adjust if near start or end
            if (start < 1) start = 1
            if (start + this.maxVisiblePages > this.totalPages) {
                start = Math.max(1, this.totalPages - this.maxVisiblePages + 1)
            }
            
            return start
        },
        paginationEnd() {
            return Math.min(this.paginationStart + this.maxVisiblePages - 1, this.totalPages)
        },
        visiblePages() {
            const pages = []
            for (let i = this.paginationStart; i <= this.paginationEnd; i++) {
                pages.push(i)
            }
            return pages
        }
    },
    methods: {
        goToPage(page) {
            // Validate page number
            page = Math.max(1, Math.min(page, this.totalPages))
            
            // Only emit if page changed
            if (page !== this.currentPage) {
                this.$emit('page-changed', page)
            }
        }
    }
}
</script>

<style scoped>
.pagination {
    gap: 0.25rem;
}

.page-link {
    min-width: 2.5rem;
    text-align: center;
    cursor: pointer;
}

.page-item.disabled .page-link {
    cursor: not-allowed;
    opacity: 0.6;
}

.page-item.active .page-link {
    font-weight: bold;
    box-shadow: 0 0 0 1px currentColor;
}
</style>