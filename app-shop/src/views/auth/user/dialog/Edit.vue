<template>
  <el-dialog title="修改人员" :visible.sync="dialogVisible" @close="dialogFormVisible" :close-on-click-modal="false" :close-on-press-escape="false">
    <!--form表单-->
    <el-form size="small" ref="form" :model="form" :rules="formRules" :label-width="formLabelWidth" v-loading="loading">
      <el-form-item label="用户名" prop="user_name">
        <el-input v-model="form.user_name" placeholder="请输入用户名"></el-input>
      </el-form-item>
      <el-form-item label="所属分组" prop="gid">
        <el-select v-model="form.gid">
          <el-option v-for="item in groupList" :value="item.id" :key="item.id" :label="item.text"></el-option>
        </el-select>
      </el-form-item>
      <el-form-item label="所属角色" prop="role">
        <el-select v-model="form.role">
          <el-option v-for="item in roleList" :value="item.role_id" :key="item.role_id" :label="item.role_name_h1"></el-option>
        </el-select>
      </el-form-item>
      <el-form-item label="登录密码" prop="password">
        <el-input v-model="form.password" placeholder="请输入登录密码" type="password"></el-input>
      </el-form-item>
      <el-form-item label="确认密码" prop="confirm_password">
        <el-input v-model="form.confirm_password" placeholder="请输入确认密码" type="password"></el-input>
      </el-form-item>
      <el-form-item label="姓名" prop="real_name">
        <el-input v-model="form.real_name"></el-input>
      </el-form-item>
    </el-form>
    <div slot="footer" class="dialog-footer">
      <el-button @click="dialogVisible = false">取 消</el-button>
      <el-button type="primary" @click="onSubmit" :loading="loading">确 定</el-button>
    </div>
  </el-dialog>
</template>

<script>
  import AuthApi from '@/api/auth.js';
  export default {
    data() {
      return {
        /*左边长度*/
        formLabelWidth: '120px',
        /*是否显示*/
        loading: false,
        /*是否显示*/
        dialogVisible: false,
        /*form表单对象*/
        form: {
          confirm_password: '',
          role: '',
          group: 0
        },
        /*form验证*/
        formRules: {
          user_name: [{
            required: true,
            message: ' ',
            trigger: 'blur'
          }],
          role: [{
            required: true,
            message: ' ',
            trigger: 'blur'
          }],
          real_name: [{
            required: true,
            message: ' ',
            trigger: 'blur'
          }]
        },
        /*角色对象*/
        roleList: [{
          role_id: 0,
          role_name_h1: '超级管理员'
        }, {
          role_id: 1,
          role_name_h1: '委员会'
        }, {
          role_id: 2,
          role_name_h1: '律师'
        }],
        groupList: null
      };
    },
    props: ['open', 'shop_user_id'],
    watch: {
      open: function (n, o) {
        if (n != o) {
          this.dialogVisible = this.open;
          if (this.open) {
            this.getBaseData();
            this.getData();
          }
        }
      }
    },
    created() {},
    methods: {
      /**
       * 获取基础数据
       */
      getBaseData: function () {
        let self = this;
        self.loading = true;
        AuthApi.userGroup({}, true)
          .then(data => {
            self.loading = false;
            self.groupList = [{
              id: 0,
              text: '无分组'
            }].concat(data.data.list.data);
          })
          .catch(error => {
            self.loading = false;
          });
      },
      /*获取数据*/
      getData() {
        let self = this;
        AuthApi.userEditInfo({
            shop_user_id: this.shop_user_id
          }).then(res => {
            self.loading = false;
            let obj = res.data.info;
            obj.password = '';
            self.form = obj;
          })
          .catch(error => {
            self.loading = false;
          });
      },

      /*修改*/
      onSubmit() {
        let self = this;
        self.$refs.form.validate((valid) => {
          if (valid) {
            self.loading = true;
            let params = self.form;
            AuthApi.userEdit(params, true)
              .then(data => {
                self.loading = false;
                self.$message({
                  message: '恭喜你，修改成功',
                  type: 'success'
                });
                self.dialogFormVisible(true);
              })
              .catch(error => {
                self.loading = false;
              });
          }
        })
      },

      /*关闭弹窗*/
      dialogFormVisible(e) {
        if (e) {
          this.$emit('close', {
            type: 'success',
            openDialog: false
          });
        } else {
          this.$emit('close', {
            type: 'error',
            openDialog: false
          });
        }
      }
    }
  };

</script>

<style></style>
