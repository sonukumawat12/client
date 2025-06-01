<template>
  <div class="card sticky-section-item">
    <h6 class="card-header">{{$t('Ticket details')}}</h6>
    <div class="card-body p-1">
        <ul class="list-group list-group-flush" >
          <li class="list-group-item p-2">
            <i class="bi bi-folder2-open"></i>
            {{$t('Category')}} :
            <strong>{{ticket.category.name}}</strong>
          </li>
          <li class="list-group-item p-2">
            <i class="bi bi-user"></i>
            {{$t('Assigned to')}}
            <strong>{{ticket.assigned_to.name}}</strong>
          </li>
          <li class="list-group-item p-2">
            <i class="bi bi-list-ul"></i>
            {{$t("Priority")}}
            <strong :class="'ticket-priority level-'+ticket.priority">{{ticket.priority}}</strong>
          </li>
          <li class="list-group-item p-2">
            <i class="bi bi-clock"></i>
            {{$t('Submitted on')}}
            <div>
              <small>{{ticket.submitted_on}}</small>
            </div>
          </li>
        </ul>
        <div v-if="ticket.attachments.length">
          <div>{{$t('Attachments')}}</div>
          <template v-for="(attachment, index) in ticket.attachments" >
          <fancy-box
              v-bind:key="index"
              v-if="['image', 'pdf'].includes(fileType(attachment.file_type))"
              :href="attachment.url"
              target="_blank"
              data-fancybox="gallery"
              class="btn btn-outline-secondary mb-1 me-2 btn-sm attachment"
            >
              <i
                v-bind:class="fileIcon(attachment.file_type)"
                class="bi bi"
              ></i>
            </fancy-box>

            <a class="btn btn-outline-secondary mb-1 me-2 btn-sm attachment" v-else :href="attachment.url" v-bind:key="index">
              <i
                v-bind:class="fileIcon(attachment.file_type)"
                class="bi bi"
              ></i>
            </a>
          </template>
        </div>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {};
  },
  props: ["ticket"],
  methods: {
    fileIcon($ftype) {
      if (this.fileType($ftype) == "pdf") return "bi-file-pdf";
      if (this.fileType($ftype) == "image") return "bi-file-image";
      if (this.fileType($ftype) == "doc") return "bi-file-word";
      if (this.fileType($ftype) == "zip") return "bi-file-archive";
      return "bi-file";
    },
    fileType($filetype){
      if ($filetype.includes("application/pdf")) return "pdf";
      if ($filetype.includes("image")) return "image";
      if ($filetype.includes("officedocument")) return "doc";
      if ($filetype.includes("zip")) return "zip";
      return null;
    },
  }
};
</script>