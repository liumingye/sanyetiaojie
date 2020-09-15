<template>
  <el-dialog title="修改分组" :visible.sync="dialogVisible" @close="dialogFormVisible" :close-on-click-modal="false" :close-on-press-escape="false">
    <!--form表单-->
    <el-form size="small" ref="form" :model="form" :rules="formRules" :label-width="formLabelWidth">
      <el-form-item label="分组名" prop="text">
        <el-input v-model="form.text" placeholder="请输入分组名"></el-input>
      </el-form-item>
      <el-form-item label="排序" prop="sort">
        <el-input v-model="form.sort" placeholder="数字越大越靠前"></el-input>
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
          text: '',
          sort: ''
        },
        /*form验证*/
        formRules: {
          text: [{
            required: true,
            message: ' ',
            trigger: 'blur'
          }]
        },
      };
    },
    props: ['open', 'editform'],
    watch: {
      open: function (n, o) {
        if (n != o) {
          this.dialogVisible = this.open;
          if (this.open) {
            this.form = this.editform;
          }
        }
      }
    },
    created() {},
    methods: {

      /*修改*/
      onSubmit() {
        let self = this;
        self.$refs.form.validate((valid) => {
          if (valid) {
            self.loading = true;
            let params = self.form;
            AuthApi.groupEdit(params, true)
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
