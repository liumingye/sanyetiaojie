<template>
  <div>
    <!--form表单-->
    <el-form size="small" ref="form" :model="form" :rules="rules" label-width="200px">
      <div class="common-form">小程序设置</div>
      <el-form-item label="关于我们" prop="aboutus">
        <div class="edit_container">
          <Uediter :text="form.aboutus" :config="ueditor.config" ref="aboutus"></Uediter>
        </div>
      </el-form-item>
      <!--提交-->
      <div class="common-button-wrapper">
        <el-button size="small" type="primary" @click="onSubmit" :loading="loading">提交</el-button>
      </div>
    </el-form>
  </div>
</template>
  
<script>
  import SettingApi from '@/api/setting.js';
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
            initialFrameHeight: 500,
          }
        },
        /*form表单数据*/
        form: {
          aboutus: '',
        },
        loading: false,
        /*验证规则*/
        rules: {

        }
      };
    },
    created() {
      this.getParams();
    },

    methods: {
      getParams() {
        let self = this;
        SettingApi.xcxDetail({}, true)
          .then(data => {
            if (data.code == 1) {
              let vars = data.data.vars.values;
              self.form = vars;
              self.$refs.aboutus.setUEContent(self.form.aboutus);
            }
          })
          .catch(error => {});
      },

      //监听复选框选中
      handleCheckedCitiesChange(val) {},

      /*设置*/
      onSubmit() {
        let self = this;
        self.form.aboutus = self.$refs.aboutus.getUEContent();
        let form = self.form;
        self.$refs.form.validate(valid => {
          if (valid) {
            self.loading = true;
            SettingApi.editXcx(form, true)
              .then(data => {
                self.loading = false;
                if (data.code == 1) {
                  self.$message({
                    message: '恭喜你，小程序设置成功',
                    type: 'success'
                  });
                } else {
                  self.loading = false;
                }
              })
              .catch(error => {
                self.loading = false;
              });
          }
        });
      }
    }
  };

</script>

<style>
  .edit_container {
    font-family: 'Avenir', Helvetica, Arial, sans-serif;
    -webkit-font-smoothing: antialiased;
    -moz-osx-font-smoothing: grayscale;
    line-height: 20px;
    color: #2c3e50;
  }
</style>
