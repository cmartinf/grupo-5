<template>
  <div class="mx-5">
    <div class="container-fluid mt-4">
      <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="text-warning mb-0">Administrar Noticias</h2>
        <div>
          <button class="btn btn-modern text-white me-2" @click="openAuthorsManagement">
            <i class="bi bi-people-fill"></i> Gestionar Autores
          </button>
          <button class="btn btn-modern text-white" @click="openAddNewsModal">
            <i class="bi bi-plus-circle"></i> Agregar Noticia
          </button>
        </div>
      </div>

      <!-- Buscar y Agregar Noticias -->
      <div class="d-flex justify-content-between align-items-center mb-4">
        <input
          type="text"
          v-model="searchQuery"
          @input="applyFilters"
          class="form-control custom-input w-50"
          placeholder="Buscar noticias..."
        />
      </div>

      <!-- Tabla de Noticias -->
      <div class="table-responsive">
        <table class="table table-bordered table-striped align-middle">
          <thead class="bg-dark text-light">
            <tr>
              <th @click="sortBy('id')" class="sortable">#</th>
              <th @click="sortBy('title')" class="sortable">Título</th>
              <th @click="sortBy('category')" class="sortable">Categoría</th>
              <th @click="sortBy('author.name')" class="sortable">Autor</th>
              <th @click="sortBy('views')" class="sortable">Vistas</th>
              <th @click="sortBy('heart_counts')" class="sortable">Likes</th>
              <th>Etiquetas</th>
              <th @click="sortBy('is_published')" class="sortable">Estado</th>
              <th>Acciones</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(news, index) in paginatedNews" :key="news.id">
              <td>{{ index + 1 + (currentPage - 1) * itemsPerPage }}</td>
              <td>{{ news.title }}</td>
              <td>{{ news.category }}</td>
              <td>
                <img
                  :src="news.author.profile_picture ? `${baseUrl}/storage/${news.author.profile_picture}` : '/default-avatar.png'"
                  alt="Autor"
                  class="rounded-circle me-2"
                  style="width: 30px; height: 30px;"
                />
                {{ news.author.name }}
              </td>
              <td>{{ news.views }}</td>
              <td>
                <i class="bi bi-heart-fill text-danger"></i> {{ news.heart_counts }}
              </td>
              <td>
                <span
                  v-for="tag in news.tags"
                  :key="tag.id"
                  class="badge custom-badge me-1"
                >
                  {{ tag.name }}
                </span>
              </td>
              <td>
                <span
                  class="badge"
                  :class="news.is_published ? 'bg-success' : 'bg-secondary'"
                >
                  {{ news.is_published ? 'Publicado' : 'Borrador' }}
                </span>
              </td>
              <td>
                <button class="btn btn-modern btn-sm me-2" @click="openEditModal(news)">
                  <i class="bi bi-pencil-square"></i>
                </button>
                <button
                  class="btn btn-sm me-2 text-white"
                  :class="news.is_published ? 'bg-success' : 'bg-danger'"
                  @click="togglePublish(news)"
                >
                  <i :class="news.is_published == '1' ? 'bi bi-x-circle bg-red' : 'bi bi-check-circle'"></i>
                </button>
                <button class="btn btn-modern btn-sm" @click="previewNews(news)">
                  <i class="bi bi-eye"></i> Vista Previa
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Controles de Paginación -->
      <nav aria-label="Page navigation">
        <ul class="pagination justify-content-center">
          <li class="page-item" :class="{ disabled: currentPage === 1 }">
            <button class="page-link" @click="goToPage(currentPage - 1)">Anterior</button>
          </li>
          <li
            v-for="page in totalPages"
            :key="page"
            class="page-item"
            :class="{ active: page === currentPage }"
          >
            <button class="page-link" @click="goToPage(page)">{{ page }}</button>
          </li>
          <li class="page-item" :class="{ disabled: currentPage === totalPages }">
            <button class="page-link" @click="goToPage(currentPage + 1)">Siguiente</button>
          </li>
        </ul>
      </nav>

      <!-- Modal Agregar Noticia -->
      <div class="modal fade custom-modal" id="addNewsModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
          <div class="modal-content custom-modal">
            <div class="modal-header custom-modal-header">
              <h5 class="modal-title">Agregar Noticia</h5>
              <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
              <form @submit.prevent="addNews">
                <div class="mb-3">
                  <label for="newTitle" class="form-label">Título</label>
                  <input required type="text" id="newTitle" class="form-control custom-input" v-model="newNews.title" />
                </div>
                <div class="mb-3">
                  <label for="newCategory" class="form-label">Categoría</label>
                  <input
                    required
                    type="text"
                    class="form-control custom-input"
                    v-model="newNews.category"
                    placeholder="Escriba o seleccione una categoría"
                    @focus="showCategoryDropdown = true"
                    @blur="hideDropdownWithDelay('category')"
                    @input="filterCategories"
                  />
                  <div
                    v-if="showCategoryDropdown"
                    class="dropdown-menu show w-100"
                    style="max-height: 200px; overflow-y: auto;"
                  >
                    <button
                      class="dropdown-item"
                      v-for="category in filteredCategories"
                      :key="category"
                      @click="selectCategory(category)"
                    >
                      {{ category }}
                    </button>
                  </div>
                </div>
                <div class="mb-3">
                  <label for="newTags" class="form-label">Etiquetas</label>
                  <div class="input-group">
                    <input 
                      type="text"
                      class="form-control custom-input"
                      v-model="tagInput"
                      placeholder="Escriba y presione Enter para agregar etiquetas"
                      @keydown.enter.prevent="addTagFromInput"
                    />
                  </div>
                  <div class="mt-2">
                    <span
                      v-for="tag in newNews.tags"
                      :key="tag"
                      class="badge custom-badge me-2"
                    >
                      {{ tag }}
                      <i class="bi bi-x-circle ms-1 cursor-pointer" @click="removeTag(tag)"></i>
                    </span>
                  </div>
                </div>
                <div class="mb-3">
                  <label for="newAuthor" class="form-label">Autor</label>
                  <select v-model="newNews.authorId" class="form-control custom-input" required>
                    <option value="">Seleccione un autor</option>
                    <option v-for="author in authors" :key="author.id" :value="author.id">
                      {{ author.name }}
                    </option>
                  </select>
                </div>
                <div class="mb-3">
                  <label for="newImage" class="form-label">Imagen</label>
                  <input required type="file" class="form-control custom-input" id="newImage" @change="onImageChange" />
                </div>
                <div class="mb-3 position-relative">
                  <label for="newContent" class="form-label">Contenido</label>
                  <div ref="addQuillEditor" class="quill-editor"></div>
                  <input required type="hidden" name="quillContent" v-model="newNews.content" />
                  <button
                    type="button"
                    class="btn btn-modern mt-3"
                    style="position: absolute; bottom: 10px; right: 10px;"
                    @click="openVideoModal"
                  >
                    <i class="bi bi-upload"></i> Generar Enlace de Video
                  </button>
                </div>
                <button type="submit" class="btn btn-modern" :disabled="loading">
                  <span v-if="loading" class="spinner-border spinner-border-sm me-2"></span>
                  Agregar Noticia
                </button>
              </form>
            </div>
          </div>
        </div>
      </div>

      <!-- Modal Subir Video -->
      <div class="modal fade custom-modal" id="uploadVideoModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-md">
          <div class="modal-content custom-modal">
            <div class="modal-header custom-modal-header">
              <h5 class="modal-title">Subir Video</h5>
              <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
              <form @submit.prevent="uploadVideo">
                <div class="mb-3">
                  <label for="videoFile" class="form-label">Seleccionar Video</label>
                  <input
                    required
                    type="file"
                    id="videoFile"
                    class="form-control custom-input"
                    accept="video/*"
                    @change="onVideoFileChange"
                  />
                </div>
                <button type="submit" class="btn btn-modern" :disabled="uploadingVideo">
                  <span v-if="uploadingVideo" class="spinner-border spinner-border-sm me-2"></span>
                  Subir
                </button>
              </form>
              <div class="mt-3" v-if="uploadedVideoUrl">
                <p class="text-success">¡Video subido exitosamente!</p>
                <div class="input-group">
                  <input
                    type="text"
                    class="form-control custom-input"
                    :value="uploadedVideoUrl"
                    readonly
                  />
                  <button class="btn btn-modern" @click="copyToClipboard">
                    Copiar Enlace
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Modal Editar Noticia -->
      <div class="modal fade custom-modal" id="editNewsModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
          <div class="modal-content custom-modal">
            <div class="modal-header custom-modal-header">
              <h5 class="modal-title">Editar Noticia</h5>
              <button
                type="button"
                class="btn-close btn-close-white"
                data-bs-dismiss="modal"
                aria-label="Cerrar"
              ></button>
            </div>
            <div class="modal-body">
              <form v-if="currentNews" @submit.prevent="updateNews">
                <div class="mb-3">
                  <label for="editTitle" class="form-label">Título</label>
                  <input
                    type="text"
                    id="editTitle"
                    class="form-control custom-input"
                    v-model="currentNews.title"
                    required
                  />
                </div>
                <div class="mb-3">
                  <label for="editCategory" class="form-label">Categoría</label>
                  <input
                    type="text"
                    class="form-control custom-input"
                    v-model="currentNews.category"
                    required
                  />
                </div>
                <div class="mb-3">
                  <label for="editTags" class="form-label">Etiquetas</label>
                  <div class="input-group">
                    <input
                      type="text"
                      class="form-control custom-input"
                      v-model="tagInput"
                      placeholder="Escriba y presione Enter para agregar etiquetas"
                      @keydown.enter.prevent="addTagFromInputForEdit"
                    />
                  </div>
                  <div class="mt-2">
                    <span
                      v-for="tag in currentNews.tags"
                      :key="tag"
                      class="badge custom-badge me-2"
                    >
                      {{ tag.name }}
                      <i class="bi bi-x-circle ms-1 cursor-pointer" @click="removeTagForEdit(tag)"></i>
                    </span>
                  </div>
                </div>          
                <div class="mb-3">
                  <label for="editAuthor" class="form-label">Autor</label>
                  <select v-model="currentNews.authorId" class="form-control custom-input" required>
                    <option value="">Seleccione un autor</option>
                    <option v-for="author in authors" :key="author.id" :value="author.id">
                      {{ author.name }}
                    </option>
                  </select>
                </div>
                <div class="mb-3">
                  <label for="editImage" class="form-label">Imagen</label>
                  <input
                    type="file"
                    class="form-control custom-input"
                    id="editImage"
                    @change="onImageChange"
                  />
                </div>
                <div class="mb-3">
                  <label for="editContent" class="form-label">Contenido</label>
                  <div ref="editQuillEditor" class="quill-editor"></div>
                  <input required type="hidden" name="quillContent" v-model="currentNews.content" />
                </div>
                <button type="submit" class="btn btn-modern" :disabled="loading">
                  <span v-if="loading" class="spinner-border spinner-border-sm me-2"></span>
                  Guardar Cambios
                </button>
              </form>
            </div>
          </div>
        </div>
      </div>

      <!-- Modal para gestión de autores -->
      <div class="modal fade custom-modal" id="authorsManagementModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl">
          <div class="modal-content custom-modal">
            <div class="modal-header custom-modal-header">
              <h5 class="modal-title">Gestión de Autores</h5>
              <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
              <div class="d-flex justify-content-between mb-4">
                <input
                  type="text"
                  v-model="authorSearchQuery"
                  @input="filterAuthors"
                  class="form-control custom-input w-50"
                  placeholder="Buscar autores..."
                />
                <button class="btn btn-modern text-white" @click="openAddAuthorModal">
                  <i class="bi bi-plus-circle"></i> Nuevo Autor
                </button>
              </div>
              
              <div class="table-responsive">
                <table class="table table-bordered">
                  <thead>
                    <tr>
                      <th>#</th>
                      <th>Nombre</th>
                      <th>Foto de Perfil</th>
                      <th>Acciones</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="(author, index) in filteredAuthors" :key="author.id">
                      <td>{{ index + 1 }}</td>
                      <td>{{ author.name }}</td>
                      <td>
                        <img 
                          :src="author.profile_picture ? `${baseUrl}/storage/${author.profile_picture}` : '/default-avatar.png'"
                          alt="Autor" 
                          class="rounded-circle"
                          style="width: 50px; height: 50px; object-fit: cover;"
                        />
                      </td>
                      <td>
                        <button class="btn btn-modern btn-sm me-2" @click="openEditAuthorModal(author)">
                          <i class="bi bi-pencil"></i> Editar
                        </button>
                        <button class="btn btn-danger btn-sm" @click="confirmDeleteAuthor(author)">
                          <i class="bi bi-trash"></i> Eliminar
                        </button>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Modal Agregar Autor -->
      <div class="modal fade custom-modal" id="addAuthorModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
          <div class="modal-content custom-modal">
            <div class="modal-header custom-modal-header">
              <h5 class="modal-title">Agregar Autor</h5>
              <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
              <form @submit.prevent="addAuthor">
                <div class="mb-3">
                  <label for="authorName" class="form-label">Nombre</label>
                  <input type="text" id="authorName" class="form-control custom-input" v-model="newAuthor.name" required />
                </div>
                <div class="mb-3">
                  <label for="authorPicture" class="form-label">Foto de Perfil</label>
                  <input type="file" class="form-control custom-input" @change="onFileChange" />
                </div>
                <button type="submit" class="btn btn-modern" :disabled="loading">
                  <span v-if="loading" class="spinner-border spinner-border-sm me-2"></span>
                  Guardar Autor
                </button>
              </form>
            </div>
          </div>
        </div>
      </div>

      <!-- Modal Editar Autor -->
      <div class="modal fade custom-modal" id="editAuthorModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
          <div class="modal-content custom-modal">
            <div class="modal-header custom-modal-header">
              <h5 class="modal-title">Editar Autor</h5>
              <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
              <form @submit.prevent="updateAuthor">
                <div class="mb-3">
                  <label for="editAuthorName" class="form-label">Nombre</label>
                  <input 
                    type="text" 
                    id="editAuthorName" 
                    class="form-control custom-input" 
                    v-model="editingAuthor.name" 
                    required
                  />
                </div>
                <div class="mb-3">
                  <label for="editAuthorPicture" class="form-label">Foto de Perfil</label>
                  <input 
                    type="file" 
                    id="editAuthorPicture" 
                    class="form-control custom-input" 
                    @change="onEditFileChange"
                  />
                  <small class="text-muted">Dejar vacío para mantener la imagen actual</small>
                  <div v-if="editingAuthor.currentPicture" class="mt-2">
                    <img 
                      :src="`${baseUrl}/storage/${editingAuthor.currentPicture}`" 
                      alt="Imagen actual" 
                      style="width: 100px; height: 100px; object-fit: cover;"
                      class="border rounded"
                    />
                  </div>
                </div>
                <button type="submit" class="btn btn-modern" :disabled="loading">
                  <span v-if="loading" class="spinner-border spinner-border-sm me-2"></span>
                  Actualizar Autor
                </button>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>











<script>
import axios from "axios";
import Quill from "quill";
import "quill/dist/quill.snow.css";
import { useToast } from "vue-toastification";

export default {
  name: "AdminNews",
 
  data() {
    return {
      quillAdd: null,
      quillEdit: null,
      newsList: [], 
      baseUrl: import.meta.env.VITE_APP_API_URL,
      filteredNews: [],
      searchQuery: "",
      authorSearchQuery: "",
      sortKey: "id",
      sortDirection: "asc",
      currentPage: 1,
      itemsPerPage: 5,
      newNews: {
        title: "",
        category: "",
        tags: [],
        author: "",
        authorId: null,
        content: "",
        image: null,
      },
      currentNews: null,
      newAuthor: {
        name: "",
        picture: null,
      },
      editingAuthor: {
        id: null,
        name: "",
        picture: null,
        currentPicture: null
      },
      categories: [],
      filteredCategories: [],
      authors: [],
      filteredAuthors: [],
      tags: [],
      filteredTags: [],
      tagInput: "",
      showCategoryDropdown: false,
      showTagDropdown: false,
      showAuthorDropdown: false,
      toast: useToast(),
      loading: false,
      uploadingVideo: false,
      uploadedVideoUrl: null,
      selectedVideoFile: null,
      addNewsModal: null,
      editNewsModal: null,
      authorsManagementModal: null,
      addAuthorModal: null,
      editAuthorModal: null,
      uploadVideoModal: null
    };
  },

  computed: {
    totalPages() {
      return Math.ceil(this.filteredNews.length / this.itemsPerPage);
    },
    paginatedNews() {
      const start = (this.currentPage - 1) * this.itemsPerPage;
      return this.filteredNews.slice(start, start + this.itemsPerPage);
    },
  },

  methods: {
    // Métodos para noticias
    fetchNews() {
      axios.get("/api/news/all").then((response) => {
        this.newsList = response.data;
        this.applyFilters();
      });
    },

    applyFilters() {
      const query = this.searchQuery.toLowerCase();
      this.filteredNews = this.newsList
        .filter(
          (news) =>
            news.title.toLowerCase().includes(query) ||
            news.category.toLowerCase().includes(query) ||
            news.author.name.toLowerCase().includes(query)
        )
        .sort((a, b) => {
          const valueA = this.getNestedValue(a, this.sortKey);
          const valueB = this.getNestedValue(b, this.sortKey);

          if (this.sortDirection === "asc") {
            return valueA > valueB ? 1 : valueA < valueB ? -1 : 0;
          } else {
            return valueA < valueB ? 1 : valueA > valueB ? -1 : 0;
          }
        });
      this.currentPage = 1;
    },

    sortBy(key) {
      if (this.sortKey === key) {
        this.sortDirection = this.sortDirection === "asc" ? "desc" : "asc";
      } else {
        this.sortKey = key;
        this.sortDirection = "asc";
      }
      this.applyFilters();
    },

    getNestedValue(object, path) {
      return path.split(".").reduce((obj, key) => (obj && obj[key] !== undefined ? obj[key] : ""), object);
    },

    goToPage(page) {
      if (page >= 1 && page <= this.totalPages) {
        this.currentPage = page;
      }
    },

    openAuthorsManagement() {
      this.fetchAuthors().then(() => {
        this.filterAuthors();
        this.authorsManagementModal.show();
      });
    },

    filterAuthors() {
      const query = this.authorSearchQuery.toLowerCase();
      this.filteredAuthors = this.authors.filter(author => 
        author.name.toLowerCase().includes(query)
      );
    },

    async fetchAuthors() {
      try {
        const response = await axios.get('/api/authors', {
          headers: {
            'Accept': 'application/json'
          }
        });
        this.authors = response.data.data || response.data;
        this.filteredAuthors = [...this.authors];
        return Promise.resolve();
      } catch (error) {
        console.error('Error fetching authors:', error);
        this.toast.error('Error al cargar la lista de autores');
        return Promise.reject(error);
      }
    },

    async updateAuthor() {
      if (!this.editingAuthor?.id) {
        this.toast.error('ID de autor no válido');
        return;
      }

      this.loading = true;
      
      const formData = new FormData();
      formData.append('name', this.editingAuthor.name);
       formData.append('_method', 'PUT');
      
      if (this.editingAuthor.picture) {
        formData.append('profile_picture', this.editingAuthor.picture);
      }

      try {
        const response = await axios.put(`/api/authors/${this.editingAuthor.id}`, formData, {
          headers: {
            'Content-Type': 'multipart/form-data',
            'Accept': 'application/json'
          }
        });

        if (response.data.success) {
          this.toast.success('Autor actualizado correctamente');
          await this.fetchAuthors();
          this.editAuthorModal.hide();
          this.authorsManagementModal.show();
        } else {
          throw new Error(response.data.message || 'Error en la respuesta del servidor');
        }
      } catch (error) {
        console.error('Error updating author:', error);
        let errorMsg = 'Error al actualizar el autor';
        
        if (error.response) {
          if (error.response.status === 404) {
            errorMsg = 'Autor no encontrado (ID inválido)';
          } else if (error.response.data?.message) {
            errorMsg = error.response.data.message;
          }
        }
        
        this.toast.error(errorMsg);
      } finally {
        this.loading = false;
      }
    },

    confirmDeleteAuthor(author) {
      if (confirm(`¿Estás seguro de que deseas eliminar al autor "${author.name}"?`)) {
        this.deleteAuthor(author.id);
      }
    },

    async deleteAuthor(id) {
      if (!id) {
        this.toast.error('ID de autor no válido');
        return;
      }

      this.loading = true;
      try {
        const response = await axios.delete(`/api/authors/${id}`, {
          headers: {
            'Accept': 'application/json'
          }
        });

        if (response.data.success) {
          this.toast.success('Autor eliminado correctamente');
          await this.fetchAuthors();
        } else {
          throw new Error(response.data.message || 'Error en la respuesta del servidor');
        }
      } catch (error) {
        console.error('Error deleting author:', error);
        let errorMsg = 'Error al eliminar el autor';
        
        if (error.response) {
          if (error.response.status === 404) {
            errorMsg = 'Autor no encontrado (puede que ya haya sido eliminado)';
          } else if (error.response.status === 409) {
            errorMsg = 'No se puede eliminar: el autor tiene noticias asociadas';
          } else if (error.response.data?.message) {
            errorMsg = error.response.data.message;
          }
        }
        
        this.toast.error(errorMsg);
      } finally {
        this.loading = false;
      }
    },

    openAddNewsModal() {
      this.newNews = {
        title: "",
        category: "",
        tags: [],
        author: "",
        authorId: null,
        content: "",
        image: null,
      };
      this.$nextTick(() => {
        if (!this.quillAdd) {
          const container = this.$refs.addQuillEditor;
          if (container) {
            this.quillAdd = new Quill(container, {
              theme: "snow",
              modules: {
                toolbar: [
                  [{ header: "1" }, { header: "2" }, { font: [] }],
                  [{ list: "ordered" }, { list: "bullet" }],
                  ["bold", "italic", "underline", "strike"],
                  [{ align: [] }],
                  ["link", "image", "video"],
                ],
              },
            });
            this.quillAdd.on("text-change", () => {
              this.newNews.content = this.quillAdd.root.innerHTML;
            });
          }
        } else {
          this.quillAdd.root.innerHTML = "";
        }
        this.addNewsModal.show();
      });
    },

    openAddAuthorModal() {
      this.newAuthor = {
        name: "",
        picture: null,
      };
      this.authorsManagementModal.hide();
      this.$nextTick(() => {
        this.addAuthorModal.show();
      });
    },

    openEditAuthorModal(author) {
      this.editingAuthor = {
        id: author.id,
        name: author.name,
        picture: null,
        currentPicture: author.profile_picture
      };
      this.authorsManagementModal.hide();
      this.$nextTick(() => {
        this.editAuthorModal.show();
      });
    },

    async addNews() {
      this.loading = true;
      const formData = new FormData();
      formData.append("title", this.newNews.title);
      formData.append("category", this.newNews.category);
      formData.append("content", this.quillAdd.root.innerHTML);
      formData.append("authorId", this.newNews.authorId);
      formData.append("tags", JSON.stringify(this.newNews.tags));

      if (this.newNews.image) {
        formData.append("image", this.newNews.image);
      }

      try {
        await axios.post("/api/news", formData);
        this.toast.success("¡Noticia agregada correctamente!");
        await this.fetchNews();
        this.addNewsModal.hide();
      } catch (error) {
        this.toast.error("Error al agregar la noticia.");
      } finally {
        this.loading = false;
      }
    },

    openEditModal(news) {
      this.currentNews = { ...news };
      this.currentNews.image = null;
      this.$nextTick(() => {
        if (!this.quillEdit) {
          const container = this.$refs.editQuillEditor;
          if (container) {
            this.quillEdit = new Quill(container, {
              theme: "snow",
              modules: {
                toolbar: [
                  [{ header: "1" }, { header: "2" }, { font: [] }],
                  [{ list: "ordered" }, { list: "bullet" }],
                  ["bold", "italic", "underline", "strike"],
                  [{ align: [] }],
                  ["link", "image"],
                ],
              },
            });
            this.quillEdit.on("text-change", () => {
              this.currentNews.content = this.quillEdit.root.innerHTML;
            });
          }
        }
        if (this.quillEdit) {
          this.quillEdit.root.innerHTML = this.currentNews.content || "";
        }
        this.editNewsModal.show();
      });
    },

    async updateNews() {
  this.loading = true;
  const formData = new FormData();
  formData.append("title", this.currentNews.title);
  formData.append("content", this.quillEdit.root.innerHTML);
  formData.append("authorId", this.currentNews.authorId);
  formData.append("category", this.currentNews.category || "");
  formData.append("tags", JSON.stringify(this.currentNews.tags));
  formData.append("_method", "PUT"); // Asegurarse de incluir esto

  if (this.currentNews.image) {
    formData.append("image", this.currentNews.image);
  }

  try {
    const response = await axios.post(`/api/news/${this.currentNews.id}`, formData, {
      headers: {
        'Content-Type': 'multipart/form-data'
      }
    });

    if (response.data.success) {
      this.toast.success("¡Noticia actualizada correctamente!");
      await this.fetchNews();
      this.editNewsModal.hide();
    } else {
      throw new Error(response.data.message || 'Error en la respuesta del servidor');
    }
  } catch (error) {
    console.error('Error updating news:', error);
    let errorMsg = "Error al actualizar la noticia";
    
    if (error.response) {
      if (error.response.data?.message) {
        errorMsg = error.response.data.message;
      } else if (error.response.status === 404) {
        errorMsg = "Noticia no encontrada";
      } else if (error.response.status === 422) {
        errorMsg = "Datos de validación incorrectos";
      }
    }
    
    this.toast.error(errorMsg);
  } finally {
    this.loading = false;
  }
},

    async togglePublish(news) {
      const updatedStatus = !news.is_published;
      try {
        await axios.put(`/api/news/${news.id}/publish`, { is_published: updatedStatus });
        await this.fetchNews();
        this.toast.success(updatedStatus ? "¡Noticia publicada!" : "Noticia despublicada");
      } catch (error) {
        this.toast.error("Error al cambiar el estado de publicación.");
      }
    },

    async addAuthor() {
      this.loading = true;
      const formData = new FormData();
      formData.append("name", this.newAuthor.name);
      if (this.newAuthor.picture) {
        formData.append("profile_picture", this.newAuthor.picture);
      }
      try {
        await axios.post("/api/authors", formData);
        await this.fetchAuthors();
        this.toast.success("¡Autor agregado correctamente!");
        this.addAuthorModal.hide();
        this.authorsManagementModal.show();
      } catch (error) {
        this.toast.error("Error al agregar el autor.");
      } finally {
        this.loading = false;
      }
    },

    openVideoModal() {
      this.selectedVideoFile = null;
      this.uploadedVideoUrl = null;
      this.$nextTick(() => {
        this.uploadVideoModal.show();
      });
    },

    onVideoFileChange(event) {
      const file = event.target.files[0];
      if (file) {
        this.selectedVideoFile = file;
      }
    },

    async uploadVideo() {
      if (!this.selectedVideoFile) {
        alert("Por favor selecciona un archivo de video.");
        return;
      }

      this.uploadingVideo = true;
      const formData = new FormData();
      formData.append("video", this.selectedVideoFile);

      try {
        const response = await axios.post("/api/videos/upload", formData, {
          headers: { "Content-Type": "multipart/form-data" },
        });

        if (response.data.success) {
          this.uploadedVideoUrl = response.data.url;
          this.toast.success("¡Video subido correctamente!");
        } else {
          this.toast.error("Error al subir el video.");
        }
      } catch (error) {
        this.toast.error("Error al subir el video.");
      } finally {
        this.uploadingVideo = false;
      }
    },

    copyToClipboard() {
      navigator.clipboard.writeText(this.uploadedVideoUrl).then(() => {
        this.toast.success("¡Enlace copiado al portapapeles!");
      });
    },

    filterCategories() {
      const query = this.newNews.category.toLowerCase();
      this.filteredCategories = this.categories.filter((category) =>
        category.toLowerCase().includes(query)
      );
    },

    selectCategory(category) {
      this.newNews.category = category;
      this.showCategoryDropdown = false;
    },

    filterTags() {
      const query = this.tagInput.toLowerCase();
      this.filteredTags = this.tags.filter((tag) => tag.toLowerCase().includes(query));
    },

    addTagFromInput() {
      const tag = this.tagInput.trim();
      if (tag && !this.newNews.tags.includes(tag)) {
        this.newNews.tags.push(tag);
      }
      this.tagInput = "";
    },

    addTagFromInputForEdit() {
      const tag = this.tagInput.trim();
      if (tag && !this.currentNews.tags.some((t) => t.name === tag)) {
        this.currentNews.tags.push({ name: tag });
      }
      this.tagInput = "";
    },

    removeTagForEdit(tag) {
      this.currentNews.tags = this.currentNews.tags.filter((t) => t.name !== tag.name);
    },

    removeTag(tag) {
      this.newNews.tags = this.newNews.tags.filter((t) => t !== tag);
    },

    fetchCategories() {
      axios.get("/api/categories").then((response) => {
        this.categories = response.data;
        this.filteredCategories = [...this.categories];
      });
    },

    fetchTags() {
      axios.get("/api/tags").then((response) => {
        this.tags = response.data.map((tag) => tag.name);
        this.filteredTags = [...this.tags];
      });
    },

    onFileChange(event) {
      const file = event.target.files[0];
      if (file) {
        this.newAuthor.picture = file;
      }
    },

    onEditFileChange(event) {
      const file = event.target.files[0];
      if (file) {
        this.editingAuthor.picture = file;
      }
    },

    previewNews(news) {
      this.$router.push({ name: "NewsArticle", params: { id: news.id } });
    },

    onImageChange(event) {
      if (event.target.files.length > 0) {
        if (this.currentNews) {
          this.currentNews.image = event.target.files[0];
        } else {
          this.newNews.image = event.target.files[0];
        }
      }
    },
  },

  mounted() {
    // Inicializar modales
    this.addNewsModal = new bootstrap.Modal(document.getElementById('addNewsModal'));
    this.editNewsModal = new bootstrap.Modal(document.getElementById('editNewsModal'));
    this.authorsManagementModal = new bootstrap.Modal(document.getElementById('authorsManagementModal'));
    this.addAuthorModal = new bootstrap.Modal(document.getElementById('addAuthorModal'));
    this.editAuthorModal = new bootstrap.Modal(document.getElementById('editAuthorModal'));
    this.uploadVideoModal = new bootstrap.Modal(document.getElementById('uploadVideoModal'));

    this.fetchCategories();
    this.fetchTags();
    this.fetchAuthors();
    this.fetchNews();
  },
};
</script>


























<style scoped>
/* Estilos modernos en azul */

/* Modal fondo y header */
.custom-modal {
  background-color: #f0f7ff;
  border-radius: 8px;
  border: none;
}

.custom-modal-header {
  background: linear-gradient(90deg, #0d6efd, #3a8dff);
  color: white;
  border-bottom: none;
  border-radius: 8px 8px 0 0;
  font-weight: 600;
  font-size: 1.25rem;
}

/* Botón de cerrar con color blanco */
.btn-close-white {
  filter: invert(1);
}

/* Inputs y selects personalizados */
.custom-input {
  border: 1.5px solid #3a8dff;
  border-radius: 6px;
  transition: border-color 0.3s ease;
}

.custom-input:focus {
  border-color: #0d6efd;
  box-shadow: 0 0 6px rgba(13, 110, 253, 0.5);
  outline: none;
}

/* Badges para tags */
.custom-badge {
  background-color: #0d6efd;
  color: white;
  font-weight: 500;
  font-size: 0.875rem;
  padding: 0.4em 0.75em;
  border-radius: 12px;
  user-select: none;
}

.cursor-pointer {
  cursor: pointer;
}

/* Botones primarios estilo moderno */
.btn-modern {
  background: linear-gradient(45deg, #0d6efd, #3a8dff);
  border: none;
  font-weight: 600;
}

/* Estilos para la tabla de autores */
.table-responsive {
  max-height: 500px;
  overflow-y: auto;
}

.table-bordered {
  border-color: #dee2e6;
}

.table-bordered th,
.table-bordered td {
  vertical-align: middle;
}

/* Estilos para el modal de gestión de autores */
#authorsManagementModal .modal-dialog {
  max-width: 90%;
}
</style>