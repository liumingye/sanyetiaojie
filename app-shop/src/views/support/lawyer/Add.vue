<template>
  <div class="pb50">
    <!--form表单-->
    <el-form size="small" ref="form" :model="form" label-width="100px">
      <div class="basic-setting-content pl16 pr16">
        <!--基本信息-->
        <div class="common-form">添加律师</div>
        <el-form-item label="姓名：" :rules="[{required: true,message: ' '}]" prop="model.name">
          <el-input v-model="form.model.name" class="max-w460"></el-input>
        </el-form-item>
        <el-form-item label="分类：" :rules="[{required: true,message: ' '}]" prop="model.category_id">
          <el-select v-model="form.model.category_id">
            <template v-for="cat in form.category">
              <el-option :value="cat.cid" :key="cat.cid" :label="cat.name"></el-option>
            </template>
          </el-select>
        </el-form-item>
        <el-form-item label="图片：" prop="image_id">
          <el-row>
            <el-button icon="el-icon-upload" @click="openUpload">选择图片</el-button>
            <div v-if="form.image_id != ''" class="img mt10">
              <img v-img-url="file_path" width="100" />
            </div>
          </el-row>
        </el-form-item>
        <el-form-item label="电话：" prop="model.phone">
          <el-input v-model="form.model.phone" class="max-w460"></el-input>
        </el-form-item>
        <el-form-item label="位置：" prop="model.location">
          <el-input v-model="form.model.location" class="max-w460"></el-input>
        </el-form-item>
        <el-form-item label="单位：" prop="model.work">
          <el-input v-model="form.model.work" class="max-w460"></el-input>
        </el-form-item>
        <el-form-item label="个人简介：" prop="model.profile">
          <el-input type="textarea" :autosize="{ minRows: 4}" v-model="form.model.profile" class="max-w460"></el-input>
        </el-form-item>
        <el-form-item label="擅长领域：" prop="model.realm">
          <el-input type="textarea" :autosize="{ minRows: 4}" v-model="form.model.realm" class="max-w460"></el-input>
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
  import LawyerApi from '@/api/lawyer.js';
  import Upload from '@/components/file/Upload';  
  export default {
    components: {
      Upload
    },
    data() {
      return {
        /*是否上传图片*/
        isupload: false,
        /*切换菜单*/
        activeIndex: '1',
        loading: false,
        /*form表单数据*/
        form: {
          model: {
            name: '',
            profile: '',
            realm: '',
            phone: '',
            location: '',
            work: '',
            category_id: '',
            image_id: ''
          },
          category: []
        },
        file_path: ''
      };
    },
    created() {
      /*获取列表*/
      this.getBaseData();
    },
    methods: {
      /**
       * 获取基础数据
       */
      getBaseData: function() {
        let self = this;
        self.loading = true;
        LawyerApi.getBaseData({}, true)
          .then(data => {
            self.loading = false;
            Object.assign(self.form, data.data);
          })
          .catch(error => {
            self.loading = false;
          });
      },
      /*提交*/
      onSubmit: function() {
        let self = this;
        let params = self.form.model;
        self.$refs.form.validate((valid) => {
          if (valid) {
            self.loading = true;
            LawyerApi.addLawyer(params, true).then(data => {
              self.loading = false;
              self.$message({
                message: '添加成功',
                type: 'success'
              });
              self.$router.push('/support/lawyer/index');
            }).catch(error => {
              self.loading = false;
            });
          }
        });
      },

      /*取消*/
      cancelFunc(){
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
