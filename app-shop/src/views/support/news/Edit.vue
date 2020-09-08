<template>
  <div class="pb50">
    <!--form表单-->
    <el-form size="small" ref="form" :model="form" label-width="100px">
      <div class="basic-setting-content pl16 pr16">
        <!--基本信息-->
        <div class="common-form">编辑新闻</div>
        <el-form-item label="标题：" :rules="[{required: true,message: ' '}]" prop="model.title">
          <el-input v-model="form.model.title" class="max-w460"></el-input>
        </el-form-item>
        <el-form-item label="作者：" prop="model.author">
          <el-input v-model="form.model.author" class="max-w460"></el-input>
        </el-form-item>
        <el-form-item label="来源：" prop="model.source">
          <el-input v-model="form.model.source" class="max-w460"></el-input>
        </el-form-item>
        <el-form-item label="公开度：" :rules="[{required: true,message: ' '}]" prop="model.status">
          <el-select v-model="form.model.status" placeholder="请选择">
            <el-option v-for="item in options" :key="item.value" :label="item.label" :value="item.value">
            </el-option>
          </el-select>
        </el-form-item>
        <el-form-item label="封面图：" prop="image_id">
          <el-row>
            <el-button icon="el-icon-upload" @click="openUpload">选择图片</el-button>
            <div v-if="form.model.image_id != 0" class="img mt10">
              <img v-img-url="file_path" height="200" />
            </div>
          </el-row>
        </el-form-item>
        <el-form-item label="内容：" :rules="[{required: true,message: ' '}]">
          <div class="edit_container">
            <Uediter :text="form.model.text" :config="ueditor.config" ref="ue"></Uediter>
          </div>
        </el-form-item>
      </div>
      <!--提交-->
      <div class="common-button-wrapper">
        <el-button type="info" size="small" @click="cancelFunc">取消</el-button>
        <el-button type="primary" size="small" @click="onSubmit" :loading="loading">发布</el-button>
      </div>
      <!--上传图片组件-->
      <Upload v-if="isupload" :isupload="isupload" :type="type" @returnImgs="returnImgsFunc">上传图片</Upload>
    </el-form>
  </div>
</template>

<script>
  import NewsApi from '@/api/news.js';
  import Uediter from '@/components/UE.vue';
  import Upload from '@/components/file/Upload';
  export default {
    components: {
      Uediter,
      Upload
    },
    data() {
      return {
        ueditor: {
          config: {
            initialFrameWidth: '100%',
            initialFrameHeight: 700,
          }
        },
        /*是否上传图片*/
        isupload: false,
        loading: false,
        /*form表单数据*/
        form: {
          model: {
            title: '',
            text: '',
            author: '',
            source: '',
            status: 1,
            image_id: 0
          },
        },
        file_path: '',
        options: [{
          value: 1,
          label: '公开'
        }, {
          value: 2,
          label: '隐藏'
        }]
      };
    },
    created() {
      /*获取列表*/
      this.getData();
    },
    methods: {
      getData: function () {
        let self = this;
        self.loading = true;
        NewsApi.getNews({
            id: this.$route.query.id
          }, true)
          .then(data => {
            self.loading = false;
            Object.assign(self.form.model, data.data);
            if (data.data.image != null) {
              self.file_path = data.data.image.file_path;
              self.form.model.image_id = data.data.image.file_id;
            }
            self.$refs.ue.setUEContent(self.form.model.text);
          })
          .catch(error => {
            self.loading = false;
          });
      },
      /*提交*/
      onSubmit: function () {
        let self = this;
        self.form.model.text = self.$refs.ue.getUEContent();
        let params = self.form.model;
        self.$refs.form.validate((valid) => {
          if (valid) {
            self.loading = true;
            NewsApi.editNews(params, true).then(data => {
              self.loading = false;
              self.$message({
                message: '编辑成功',
                type: 'success'
              });
              self.$router.push('/support/news/index');
            }).catch(error => {
              self.loading = false;
            });
          }
        });
      },

      /*取消*/
      cancelFunc() {
        this.$router.go(-1);
      },

      /*上传*/
      openUpload(e) {
        this.type = e;
        this.isupload = true;
      },

      /*获取图片*/
      returnImgsFunc(e) {
        if (e != null && e.length > 0) {
          this.file_path = e[0].file_path;
          this.form.model.image_id = e[0].file_id;
        }
        this.isupload = false;
      }
    }
  };

</script>

<style lang="scss" scoped>
  .edit_container {
    font-family: 'Avenir', Helvetica, Arial, sans-serif;
    -webkit-font-smoothing: antialiased;
    -moz-osx-font-smoothing: grayscale;
    line-height: 20px;
    color: #2c3e50;
  }

</style>
