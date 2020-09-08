<template>
  <div style="padding-bottom:100px;">
    <div v-loading="loading">
      <div class="common-form">基本信息</div>
      <div class="table-wrap">
        <el-row>
          <el-col :md="8" :lg="5">
            <div class="pb16">
              <span class="gray9">ID：</span>
              {{ detail.id }}
            </div>
          </el-col>
          <el-col :md="8" :lg="5">
            <div class="pb16">
              <span class="gray9">用户：</span>
              {{ detail.user.nickName }}
              <span>用户ID：({{ detail.user.user_id }})</span>
            </div>
          </el-col>
          <el-col :md="8" :lg="5">
            <div class="pb16">
              <span class="gray9">留言时间：</span>
              {{ detail.create_time }}
            </div>
          </el-col>
        </el-row>
      </div>
      <div class="common-form">留言</div>
      <el-row>
        <el-col>
          <div class="pb16">
            {{ detail.text }}
          </div>
        </el-col>
      </el-row>
      <div class="common-form mt10" v-if="detail.image.length > 0">附件</div>
      <el-row>
        <el-col v-for="(item,index) in detail.image" :key="index" class="file-col">
          <a :href="item.file_path" target="_blank">
            <video class="mr10 mb10" :src="item.file_path" v-if="item.file_type == 'video'"></video>
            <el-image class="mr10 mb10" :src="item.file_path" v-else-if="item.file_type == 'image'"></el-image>
            <el-image class="mr10 mb10" :src="fileimg_url" v-else></el-image>
          </a>
        </el-col>
      </el-row>
    </div>
    <div class="common-button-wrapper">
      <el-button type="info" size="small" @click="cancelFunc">返回上一页</el-button>
    </div>
  </div>
</template>

<script>
  import FeedbackApi from '@/api/feedback.js';
  import fileimg from "@/assets/img/icon/file.png";

  export default {
    components: {},
    data() {
      return {
        fileimg_url: fileimg,
        /*是否加载完成*/
        loading: true,
        /*意见数据*/
        detail: {
          user: {},
          image: {}
        },
      };
    },
    created() {
      /*获取列表*/
      this.getData();
    },
    methods: {
      /*获取参数*/
      getData() {
        let self = this;
        self.loading = true;
        // 取到路由带过来的参数
        const params = this.$route.query.id;
        FeedbackApi.feedbackdetail({
              id: params,
            },
            true
          )
          .then((data) => {
            self.loading = false;
            self.detail = data.data.detail;
          })
          .catch((error) => {
            self.loading = false;
          });
      },
      /*返回*/
      cancelFunc() {
        this.$router.go(-1);
      },
    },
  };

</script>

<style lang="scss" scoped>
  span[contenteditable] {
    display: inline-block;
    min-width: 40px;
  }

  .file-col {
    width: auto;
  }

  .file-col /deep/ img,
  .file-col /deep/ video {
    width: 100px;
    height: 100px;
    background-color: #fafafa;
    object-fit: cover;
  }
  /deep/ .el-timeline-item__tail{
    display: block!important;
  }

</style>
