<template>
    <div class="min-h-screen flex flex-col bg-[#f0f4f8]">
        <Navbar />

        <div class="flex-grow container mx-auto p-6">
            <div v-if="book" class="max-w-5xl mx-auto bg-white p-6 rounded-xl shadow-lg flex flex-col lg:flex-row gap-8">
                <!-- Grāmatas attēls un vērtējums -->
                <div class="w-full lg:w-1/3 flex flex-col items-center">
                    <img
                    :src="imageUrl"
                    alt="Book cover"
                    class="w-full h-auto object-cover rounded-lg shadow mb-4"
                    />

                    <div v-if="$page.props.auth.user" class="flex flex-col items-center mt-4">
                        <div class="flex items-center space-x-1">
                            <label v-for="n in 5" :key="n" class="cursor-pointer transition-transform hover:scale-110">
                                <input
                                    type="radio"
                                    :value="n"
                                    v-model="selectedRating"
                                    @change="saveRating"
                                    class="hidden"
                                />
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    class="w-6 h-6"
                                    :class="n <= selectedRating ? 'text-yellow-400' : 'text-gray-300'"
                                    fill="currentColor"
                                    viewBox="0 0 20 20"
                                >
                                    <path
                                        d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.178 3.622a1 1 0 00.95.69h3.805c.969 0 1.371 1.24.588 1.81l-3.077 2.234a1 1 0 00-.364 1.118l1.179 3.623c.3.921-.755 1.688-1.54 1.118l-3.077-2.234a1 1 0 00-1.176 0l-3.077 2.234c-.784.57-1.838-.197-1.54-1.118l1.179-3.623a1 1 0 00-.364-1.118L2.43 9.05c-.783-.57-.38-1.81.588-1.81h3.805a1 1 0 00.95-.69l1.178-3.622z"
                                    />
                                </svg>
                            </label>
                        </div>
                        
                        <p v-if="ratingSaved" class="text-green-600 text-sm mt-1">Vērtējums saglabāts!</p>
                    </div>
                </div>

                <!-- Saturs -->
                <div class="w-full lg:w-2/3 flex flex-col">
                    <h1 class="text-3xl font-bold text-[#213555] mb-2">{{ book.title }}</h1>
                    <p class="text-lg text-gray-700 italic mb-4">{{ book.author }}</p>

                    <!-- Žanri -->
                    <div v-if="book.genres?.length" class="flex flex-wrap gap-2 mb-4">
            <span
                v-for="genre in book.genres"
                :key="genre.id"
                class="bg-[#e0ecff] text-[#213555] px-3 py-1 rounded-full text-xs font-medium"
            >
              {{ genre.name }}
            </span>
                    </div>

                    <p class="text-gray-600 mb-6">{{ book.description }}</p>

                    <!-- Mape + komentārs -->
                    <div v-if="$page.props.auth.user">
                        <!-- Mape -->
                        <div class="relative mt-6">

                        <!-- POGA -->
                        <button
                            @click.stop="toggleFolderDropdown"
                            :disabled="user?.restricted"
                            class="flex items-center gap-2 border border-gray-300 px-4 py-2 rounded-xl bg-white hover:bg-gray-50 shadow-sm text-[#213555] disabled:opacity-50 disabled:cursor-not-allowed"
                        >
                        <span class="material-icons-outlined">folder</span>
                            Pievienot mapē
                        </button>

                        <!-- DROPDOWN -->
                        <div
                            v-if="showFolderDropdown && !user?.restricted"
                            ref="dropdown"
                            class="absolute mt-2 w-56 bg-white border rounded-xl shadow-lg z-20"
                        >
                        
                            <div
                                v-for="folder in folders"
                                :key="folder.id"
                                @click="selectFolder(folder.id)"
                                class="flex justify-between items-center px-4 py-2 hover:bg-gray-100 cursor-pointer text-sm"
                            >

                                <span>{{ folder.name }}</span>

                                <!-- IKONA -->
                                <span>
                                    <span v-if="savedFolderIds.includes(folder.id)"
                                        class="material-icons-outlined text-red-400 text-[18px]">
                                        remove
                                    </span>

                                    <span v-else
                                        class="material-icons-outlined text-green-500 text-[18px]">
                                        add
                                    </span>
                                </span>

                            </div>
                        </div>

                    </div>

                    <p v-if="isSavedToFolder" class="text-green-600 mt-2 text-sm">
                        Pievienots mapē.
                    </p>
                    <p v-if="removedFromFolder" class="text-red-500 mt-2 text-sm">
                        Izņemta no mapes.
                    </p>
                    <!-- Vidējais vērtējums -->
                    <div v-if="averageRating !== null" class="mt-6 flex items-center gap-3">

                        <div class="flex items-center">
                            <span
                                v-for="n in 5"
                                :key="n"
                                class="material-icons text-[20px]"
                                :class="n <= Math.round(averageRating) ? 'text-yellow-400' : 'text-gray-300'"
                            >
                                star
                            </span>
                        </div>

                        <!-- skaitlis -->
                        <span class="text-lg font-semibold text-[#213555]">
                            {{ averageRating.toFixed(1) }}
                        </span>

                        <span class="text-sm text-gray-400">
                            ({{ ratingsCount }} vērtējumi)
                        </span>
                        <!--  skaits -->
                        <span class="text-sm text-gray-400">
                            ({{ comments.length }} atsauksmes)
                        </span>


                    </div>

                        <!-- Komentārs -->
                        <div class="mt-4">
                            <!-- <label class="block text-sm font-medium mb-1 text-gray-800">Tavs komentārs:</label> -->
                            <div class="mt-6 flex items-start gap-3">

                                <!-- Avatar -->
                                <div class="w-9 h-9 rounded-full bg-[#213555] flex items-center justify-center text-white text-sm font-bold">
                                    {{ $page.props.auth.user.username.charAt(0).toUpperCase() }}
                                </div>

                                <!-- Input zona -->
                                <div class="flex-1">

                                    <!-- TEXTAREA -->
                                    <textarea
                                        ref="textarea"
                                        v-model="comment"
                                        rows="3"
                                        placeholder="Uzraksti savu atsauksmi..."
                                        :disabled="user?.restricted"
                                        class="w-full resize-none border border-gray-300 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-[#213555] disabled:bg-gray-200 disabled:cursor-not-allowed"
                                        @keydown.enter.prevent="handleEnter"
                                    />
                                    <p v-if="user?.restricted" class="text-red-500 text-sm mt-2">
                                        Tu nevari rakstīt komentārus, jo Tavs konts ir ierobežots
                                    </p>

                                    <!-- POGU RINDA -->
                                    <div class="flex justify-between items-center mt-2">

                                        <!-- Kreisā puse -->
                                        <button
                                            @click="addSpoiler"
                                            class="text-xs text-blue-600 border border-blue-200 px-2 py-1 rounded hover:bg-blue-50 transition"
                                        >
                                            Noslēpt sižetu
                                        </button>

                                        <!-- Labā puse -->
                                        <button
                                            @click="submitFeedback"
                                            :disabled="user?.restricted"
                                            class="bg-[#213555] hover:bg-[#3e5879] text-white px-4 py-1.5 rounded-lg text-xs disabled:opacity-50 disabled:cursor-not-allowed"
                                        >
                                            Sūtīt
                                        </button>

                                    </div>

                                    <!-- Helper -->
                                    <p class="text-xs text-gray-400 mt-1">
                                        Iezīmē tekstu un spied "Noslēpt sižetu", lai noslēptu sižetu.
                                    </p>

                                </div>

                            </div>
                        </div>
                    </div>

                    <!-- Ja nav pieslēdzies -->
                    <div v-else class="mt-6 bg-yellow-100 border border-yellow-400 p-4 rounded">
                        Lai komentētu vai vērtētu šo grāmatu, lūdzu
                        <a href="/auth" class="text-blue-600 font-semibold underline">ienāc sistēmā</a>.
                    </div>

                    

                    <!-- Atsauksmes -->
                    <div class="mt-10">
    <h2 class="text-2xl font-semibold text-[#213555] mb-4">Lasītāju atsauksmes</h2>
    <p v-if="reportSent"
        class="bg-green-100 text-green-700 px-3 py-2 rounded-lg text-sm mb-3 inline-block">
        Ziņojums nosūtīts
    </p>

    <div v-if="comments.length" class="space-y-3">

        <div
    v-for="c in showAllComments ? comments : comments.slice(0, visibleCommentsCount)"
    :key="c.id"
    :id="`comment-${c.id}`"
    class="flex gap-4 py-4 border-b"
>

    <!-- Avatar -->
    <a
        :href="`/users/${c.user.id}`"
        class="w-10 h-10 rounded-full bg-gray-200 text-gray-700 flex items-center justify-center text-sm font-semibold hover:bg-[#213555] hover:text-white transition"
    >
        {{ c.user.username.charAt(0).toUpperCase() }}
    </a>

    <!-- Saturs -->
    <div class="flex-1">

        <!-- Username + time -->
        <div class="flex justify-between items-center">
    
    <!-- kreisā puse -->
    <div class="flex items-center gap-2">
        <a
            :href="`/users/${c.user.id}`"
            class="font-semibold text-[#213555] text-sm hover:underline hover:text-blue-600 transition"
        >
            {{ c.user.username }}
        </a>

        <span class="text-gray-400 text-xs">
            • {{ formatDate(c.created_at) }}
        </span>
    </div>

    <!-- LABĀ PUSE (⋯) -->
    <div class="relative">
        <button
            @click="toggleMenu(c.id)"
            class="text-gray-400 hover:text-gray-600 text-lg"
        >
            ⋯
        </button>

        <div
            v-if="activeMenu === c.id"
            class="absolute right-0 mt-2 w-40 bg-white rounded-xl shadow-lg border border-gray-100 z-20 overflow-hidden"lass="absolute right-0 mt-2 w-32 bg-white border rounded shadow-md z-10"
        >
            <button
                v-if="$page.props.auth.user &&
                    (c.user_id === $page.props.auth.user.id ||
                    $page.props.auth.user.role === 'admin')"
                @click="confirmDelete(c.id)"
                class="flex items-center gap-2 w-full px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition"
            >
                <span class="material-icons-outlined text-[18px]">delete</span>
                Dzēst
            </button>

            <button
                v-if="$page.props.auth.user &&
                    c.user_id !== $page.props.auth.user.id"
                @click="openReportModal(c.id)"
                class="flex items-center gap-2 w-full px-4 py-2 text-sm text-orange-500 hover:bg-orange-50 transition"
            >
                <span class="material-icons-outlined text-[18px]">flag</span>
                Ziņot
            </button>
        </div>
    </div>

</div>

        <!-- Teksts -->
        <div class="text-gray-700 text-sm mt-1 leading-relaxed"
            v-html="formatComment(c.comment)">
        </div>

        <!-- Actions -->
        <div class="flex items-center gap-4 mt-2 text-xs">

            <button
                @click="likeComment(c.id)"
                class="flex items-center gap-1 transition"
            >
                <span class="material-icons text-[18px]">
                    {{ c.is_liked ? 'thumb_up' : 'thumb_up_off_alt' }}
                </span>
                <span class="text-gray-600">
                    {{ c.likes_count ?? 0 }}
                </span>
            </button>

        </div>
    </div>
</div>

        <!-- Rādīt vairāk -->
        <div
            v-if="comments.length > 2"
            ref="commentsToggle"
            class="text-center mt-4"
        >
            <button
                @click="toggleComments"
                class="text-sm text-[#213555] font-medium hover:underline"
            >
                {{ showAllComments ? 'Rādīt mazāk ↑' : 'Rādīt vairāk ↓' }}
            </button>
        </div>

    </div>

    <div v-else class="text-gray-500">
        Vēl nav atsauksmju. Esi pirmais!
    </div>
</div>

<div
    v-if="showFloatingCollapse && showAllComments"
    class="fixed bottom-6 right-6 z-50"
>
    <button
        @click="toggleComments"
        class="bg-[#213555] hover:bg-[#3e5879] text-white px-4 py-2 rounded-full shadow-lg text-sm transition"
    >
        ↑ Rādīt mazāk
    </button>
</div>

                    <div v-if="deleteModalVisible"
     class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm">

    <div class="bg-white rounded-2xl shadow-xl w-full max-w-md overflow-hidden">

        <!-- HEADER -->
        <div class="bg-[#213555] text-white px-6 py-4 flex items-center gap-3">
            <span class="material-icons">warning</span>
            <h2 class="text-lg font-semibold">Apstiprināt dzēšanu</h2>
        </div>

        <!-- CONTENT -->
        <div class="p-6 text-gray-700 text-sm">
            Vai tiešām vēlies dzēst šo komentāru?  
            <br>
            <span class="text-gray-400 text-xs">Šo darbību nevar atsaukt.</span>
        </div>

        <!-- ACTIONS -->
        <div class="flex justify-end gap-3 px-6 pb-6">

            <!-- Atcelt -->
            <button
                @click="cancelDelete"
                class="px-4 py-2 rounded-lg border border-gray-300 text-gray-600 hover:bg-gray-100 transition text-sm"
            >
                Atcelt
            </button>

            <!-- Dzēst -->
            <button
                @click="deleteCommentConfirmed"
                class="px-4 py-2 rounded-lg bg-red-500 hover:bg-red-600 text-white transition text-sm flex items-center gap-1"
            >
                <span class="material-icons text-[16px]">delete</span>
                Dzēst
            </button>

        </div>
    </div>
</div>

                    <!-- REPORT MODAL -->
<div v-if="reportModalVisible"
     class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm">

    <div class="bg-white rounded-2xl shadow-xl w-full max-w-md overflow-hidden">

        <!-- HEADER -->
        <div class="bg-[#213555] text-white px-6 py-4 flex items-center gap-3">
            <span class="material-icons">flag</span>
            <h2 class="text-lg font-semibold">Ziņot par komentāru</h2>
        </div>

        <!-- CONTENT -->
        <div class="p-6 text-sm text-gray-700 space-y-3">

            <label class="block font-medium mb-2">Izvēlies iemeslu:</label>

            <!-- OPTIONS -->
            <div class="space-y-2">

                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="radio" value="rupjības" v-model="reportReasonType">
                    Rupjības vai aizskarošs saturs
                </label>

                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="radio" value="spoiler" v-model="reportReasonType">
                    Atklāj sižetu
                </label>

                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="radio" value="spam" v-model="reportReasonType">
                    Spams vai reklāma
                </label>

                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="radio" value="cits" v-model="reportReasonType">
                    Cits
                </label>

            </div>

            <!-- TEXTAREA tikai ja "Cits" -->
            <textarea
                v-if="reportReasonType === 'cits'"
                v-model="reportReason"
                class="w-full border rounded p-3 mt-3"
                rows="3"
                placeholder="Apraksti iemeslu..."
            ></textarea>

        </div>

        <!-- ACTIONS -->
        <div class="flex justify-end gap-3 px-6 pb-6">

            <button
                @click="reportModalVisible = false"
                class="px-4 py-2 border rounded-lg text-gray-600 hover:bg-gray-100 text-sm"
            >
                Atcelt
            </button>

            <button
                @click="submitReport"
                class="px-4 py-2 bg-red-500 hover:bg-red-600 text-white rounded-lg text-sm flex items-center gap-1"
            >
                <span class="material-icons text-[16px]">send</span>
                Nosūtīt
            </button>

        </div>
    </div>
</div>

                </div>
            </div>

            <div v-else class="text-center text-gray-500">Notiek ielāde...</div>
        </div>
    </div>
</template>



<script>
import axios from 'axios';
import Navbar from "@/Components/Navbar.vue";
import { usePage } from '@inertiajs/vue3'



export default {
    components: { Navbar },
    props: ['id'],
    data() {
        return {
            book: null,
            rating: 0,
            selectedRating: 0,
            ratingSaved: false,
            comment: '',
            comments: [],
            averageRating: null,
            folders: [],
            isSavedToFolder: false,
            visibleCommentsCount: 2,
            showAllComments: false,
            deleteModalVisible: false,
            commentToDeleteId: null,
            activeMenu: null,
            reportModalVisible: false,
            reportCommentId: null,
            reportReason: '',
            highlightedCommentId: null,
            showFolderDropdown: false,
            removedFromFolder: false,
            showFloatingCollapse: false,
            ratingsCount: 0,
            reportReasonType: '',
            reportSent: false,

        };
    },
    mounted() {
    this.fetchBook();
    this.fetchRatings();
    this.fetchComments();
    this.fetchFolders();
    this.fetchUserRating();

    if (this.$page.props.auth.user) {
        this.fetchUserRating();
    }

    // 🔥 HASH APSTRĀDE (ja atvērts no admin ar #comment-XX)
    this.$nextTick(() => {
        const hash = window.location.hash;

        if (hash && hash.startsWith('#comment-')) {
            const id = hash.replace('#comment-', '');

            this.highlightedCommentId = parseInt(id);

            // automātiski parāda visus komentārus
            this.showAllComments = true;

            // pēc 4 sekundēm noņem highlight
            setTimeout(() => {
                this.highlightedCommentId = null;
            }, 4000);
        }
    });
    document.addEventListener('click', this.handleClickOutside);

    this.handleSpoilerClick = (e) => {
    const box = e.target.closest('.spoiler-box');
    if (box) {
        box.classList.toggle('revealed');
    }
};

document.addEventListener('click', this.handleSpoilerClick);

window.addEventListener('scroll', this.handleScroll);
},
    beforeUnmount() {
    document.removeEventListener('click', this.handleClickOutside);
    document.removeEventListener('click', this.handleSpoilerClick);
    window.removeEventListener('scroll', this.handleScroll);
},

    methods: {
        fetchBook() {
            axios.get(`/books/${this.id}`).then(res => this.book = res.data);
        },
        formatComment(text) {
            if (!text) return '';

            return text.replace(
                /\[spoiler\](.*?)\[\/spoiler\]/g,
                `<span class="spoiler-box">
                    <span class="spoiler-text">$1</span>
                </span>`
            );
        },
        fetchRatings() {
            axios.get(`/books/${this.id}/ratings`).then(res => {
                const ratings = res.data;

                this.ratingsCount = ratings.length; // 🔥 ŠIS JAUNS

                if (ratings.length) {
                    const total = ratings.reduce((sum, r) => sum + Number(r.rating), 0);
                    this.averageRating = total / ratings.length;
                } else {
                    this.averageRating = null;
                }
            });
        },
        fetchUserRating() {
            axios.get(`/books/${this.id}/my-rating`)
                .then(res => {
                    if (res.data && res.data.rating) {
                        this.selectedRating = res.data.rating;
                        this.rating = res.data.rating;
                    }
                })
                .catch(error => {
                    console.error("Kļūda ielādējot my-rating:", error);
                });
        },
        saveRating() {
            if (!this.selectedRating) return;
            if (this.selectedRating === this.rating) return;

            axios.post(`/books/${this.book.id}/ratings`, {
                rating: this.selectedRating
            })
            .then(() => {
                this.rating = this.selectedRating;
                this.ratingSaved = true;
                this.fetchRatings();

                setTimeout(() => this.ratingSaved = false, 2000);
            })
            .catch(error => {
                console.error(error);
            });
        },

        fetchComments() {
            axios.get(`/books/${this.id}/comments`).then(res => this.comments = res.data);
        },
        fetchFolders() {
            axios.get('/folders').then(res => this.folders = res.data);
        },

        submitFeedback() {
    if (!this.comment) return alert("Lūdzu ieraksti komentāru!");

    axios.post(`/books/${this.book.id}/comments`, { comment: this.comment })
        .then(() => {
            this.fetchComments();
            this.comment = '';
            this.visibleCommentsCount = 2;
            this.showAllComments = false;
        })
        .catch(error => {
            if (error.response && error.response.status === 403) {
                alert(error.response.data.message);
            } else {
                console.error(error);
            }
        });
},


        confirmDelete(commentId) {
            this.commentToDeleteId = commentId;
            this.deleteModalVisible = true;
        },
        deleteCommentConfirmed() {
            axios.delete(`/comments/${this.commentToDeleteId}`)
                .then(() => {
                    // Noņem komentāru no vietējā saraksta
                    this.comments = this.comments.filter(c => c.id !== this.commentToDeleteId)
                    this.deleteModalVisible = false
                    this.commentToDeleteId = null
                })
                .catch(error => {
                    console.error("Neizdevās izdzēst komentāru:", error)
                })
        }
        ,
        cancelDelete() {
            this.deleteModalVisible = false;
            this.commentToDeleteId = null;
        },
        toggleComments() {
            this.showAllComments = !this.showAllComments;
        },

        likeComment(commentId) {
            axios.post(`/comments/${commentId}/like`)
                .then(res => {
                    const index = this.comments.findIndex(c => c.id === commentId);

                    if (index !== -1) {
                        this.comments[index].likes_count = res.data.likes_count;
                        this.comments[index].is_liked = res.data.is_liked;
                    }
                });
        },
toggleMenu(commentId) {
    this.activeMenu = this.activeMenu === commentId ? null : commentId;
},

openReportModal(commentId) {
    this.reportCommentId = commentId;
    this.reportReason = '';
    this.reportModalVisible = true;
    this.activeMenu = null;
},

submitReport() {
    let finalReason = this.reportReasonType;

    // ja "cits" → ņem textarea
    if (this.reportReasonType === 'cits') {
        if (!this.reportReason) {
            alert('Lūdzu ievadi iemeslu');
            return;
        }
        finalReason = this.reportReason;
    }

    if (!this.reportReasonType) {
        alert('Lūdzu izvēlies iemeslu');
        return;
    }

    axios.post(`/comments/${this.reportCommentId}/report`, {
        reason: finalReason
    })
    .then(() => {
        this.reportModalVisible = false;
        this.reportReason = '';
        this.reportReasonType = '';

        // 🔥 parādām tekstu
        this.reportSent = true;

        setTimeout(() => {
            this.reportSent = false;
        }, 2000);
    })
    .catch(error => {
        alert(error.response?.data?.message || 'Kļūda');
    });
},
toggleFolderDropdown() {
    this.showFolderDropdown = !this.showFolderDropdown;
},

selectFolder(folderId) {
    if (this.user?.restricted) {
    alert('Tavs konts ir ierobežots. Tu nevari izmantot mapes.')
    return
}
    this.showFolderDropdown = false;

    if (this.savedFolderIds.includes(folderId)) {

    axios.delete(`/folders/${folderId}/books/${this.book.id}`)
    .then(() => {
        this.removedFromFolder = true;

        this.fetchBook();

        setTimeout(() => {
            this.removedFromFolder = false;
        }, 2000);
    })
    .catch(error => {
        console.error("Kļūda dzēšot:", error);
    });

    return;
}

    axios.post(`/folders/${folderId}/books`, {
        book_id: this.book.id
    })
    .then(() => {
        this.isSavedToFolder = true;

        this.fetchBook();

        setTimeout(() => {
            this.isSavedToFolder = false;
        }, 2000);
    })
    .catch(error => {
        console.error("Kļūda:", error);
    });
},
handleClickOutside(event) {
    if (!this.$refs.dropdown) return;
    if (!this.$refs.dropdown.contains(event.target)) {
        this.showFolderDropdown = false;
    }
},
formatDate(date) {
    const d = new Date(date);
    const now = new Date();
    const diff = Math.floor((now - d) / 1000);

    if (diff < 60) return 'tikko';
    if (diff < 3600) return `${Math.floor(diff / 60)} min`;
    if (diff < 86400) return `${Math.floor(diff / 3600)} h`;
    if (diff < 604800) return `${Math.floor(diff / 86400)} d`;

    return d.toLocaleDateString();
},
addSpoiler() {
    const start = this.$refs.textarea.selectionStart;
    const end = this.$refs.textarea.selectionEnd;

    const before = this.comment.substring(0, start);
    const selected = this.comment.substring(start, end);
    const after = this.comment.substring(end);

    if (!selected.trim()) {
        alert("Lūdzu iezīmē tekstu, ko vēlies paslēpt kā spoileri!");
        return;
    }

    this.comment = `${before}[spoiler]${selected}[/spoiler]${after}`;
},
handleEnter(e) {
    if (e.shiftKey) return;

    if (!this.comment.trim()) return;

    this.submitFeedback();
},
handleScroll() {
    const scrollY = window.scrollY;
    if (!this.$refs.commentsToggle) {
        this.showFloatingCollapse = scrollY > 400;
        return;
    }
    const rect = this.$refs.commentsToggle.getBoundingClientRect();
    const isVisible =
        rect.top >= 0 &&
        rect.bottom <= window.innerHeight;

    if (isVisible) {
        this.showFloatingCollapse = false;
    } else {
        this.showFloatingCollapse = scrollY > 400;
    }
},

    },
    watch: {
        id() {
            this.fetchBook();
            this.fetchRatings();
            this.fetchComments();
            this.fetchFolders();
            this.fetchUserRating();
        }
    },

    // 👇 PIEVIENO ŠEIT
    computed: {
        user() {
        return this.$page.props.auth?.user
    },
        imageUrl() {
            const img = this.book?.image;
            if (!img) return 'https://via.placeholder.com/300';
            // ja bilde ir no Google (pilns URL), atgriežam to tieši
            return img.startsWith('http') ? img : `/${img}`;
        },
        savedFolderIds() {
        return this.book?.folders?.map(f => f.id) || [];
    }
    },
};
</script>
<style>
select:disabled {
    background-color: #f3f4f6;
    cursor: not-allowed;
}
svg {
    transition: transform 0.2s ease, color 0.3s ease;
}

/* KONTEINERS */
.spoiler-box {
    cursor: pointer;
}

/* TEKSTS */
.spoiler-text {
    filter: blur(6px);
    transition: all 0.3s ease;
    background: #e5e7eb;
    border-radius: 4px;
    padding: 0 4px;
}

/* HOVER → mazāk blur */
.spoiler-box:hover .spoiler-text {
    filter: blur(3px);
}

/* CLICK → pilnībā redzams */
.spoiler-box.revealed .spoiler-text {
    filter: blur(0);
    background: transparent;
}

textarea {
    background-image: linear-gradient(
        to right,
        transparent 0%,
        transparent 100%
    );
}

textarea::selection {
    background: #dbeafe; 
}


</style>
