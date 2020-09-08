<template>
  <div class="pb50">
    <!--form表单-->
    <el-form size="small" ref="form" :model="form" label-width="100px">
      <div class="basic-setting-content pl16 pr16">
        <!--基本信息-->
        <div class="common-form">编辑问题</div>
        <el-form-item label="标题：" :rules="[{required: true,message: ' '}]" prop="model.title">
          <el-input v-model="form.model.title" class="max-w460"></el-input>
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

    </el-form>
  </div>
</template>

<script>
  import QuestionsApi from '@/api/questions.js';
  import Uediter from '@/components/UE.vue';
  export default {
    components: {
      Uediter,
    },
    data() {
      return {
        ueditor: {
          config: {
            initialFrameWidth: '100%',
            initialFrameHeight: 700,
          }
        },
        loading: false,
        /*form表单数据*/
        form: {
          model: {
            title: '',
            text: '',
          },
        },
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
        QuestionsApi.getQuestions({
            id: this.$route.query.id
          }, true)
          .then(data => {
            self.loading = false;
            Object.assign(self.form.model, data.data);
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
            QuestionsApi.editQuestions(params, true).then(data => {
              self.loading = false;
              self.$message({
                message: '编辑成功',
                type: 'success'
              });
              self.$router.push('/support/questions/index');
            }).catch(error => {
              self.loading = false;
            });
          }
        });
      },

      /*取消*/
      cancelFunc() {
        this.$router.go(-1);
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
