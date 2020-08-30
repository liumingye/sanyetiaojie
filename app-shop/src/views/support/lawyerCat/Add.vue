<template>
  <el-dialog title="添加分类" :visible.sync="dialogVisible" @close="dialogFormVisible" :close-on-click-modal="false"
    :close-on-press-escape="false">
    <el-form size="small" :model="form" :rules="formRules" ref="form">
      <el-form-item label="分类名称" prop="name" :label-width="formLabelWidth">
        <el-input v-model="form.name" autocomplete="off"></el-input>
      </el-form-item>
      <el-form-item label="分类排序" prop="sort" :label-width="formLabelWidth">
        <el-input v-model.number="form.sort" autocomplete="off"></el-input>
      </el-form-item>
    </el-form>
    <div slot="footer" class="dialog-footer">
      <el-button @click="dialogFormVisible">取 消</el-button>
      <el-button type="primary" @click="addUser" :loading="loading">确 定</el-button>
    </div>
  </el-dialog>

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
        /*表单数据对象*/
        form: {
          name: '',
          sort: 100
        },
        /*验证规则*/
        formRules: {
          name: [{
            required: true,
            message: '请输入分类名称',
            trigger: 'blur'
          }],
          sort: [{
            required: true,
            message: '分类排序不能为空'
          }, {
            type: 'number',
            message: '分类排序必须为数字'
          }]
        },
        /*左边长度*/
        formLabelWidth: '120px',
        /*是否显示*/
        dialogVisible: false,
        /*是否加载完成*/
        loading: false
      };
    },
    props: ['open_add', 'addform'],
    created() {
      this.dialogVisible = this.open_add;
    },
    methods: {
      /*添加商品类别*/
      addUser() {
        let self = this;
        let params = self.form;
        self.$refs.form.validate((valid) => {
          if (valid) {
            self.loading = true;
            LawyerApi.catAdd(params).then(data => {
              self.loading = false;
              self.$message({
                message: '添加成功',
                type: 'success'
              });
              self.dialogFormVisible(true);
            }).catch(error => {
              self.loading = false;
            });
          }
        });
      },
      /*关闭弹窗*/
      dialogFormVisible(e) {
        if (e) {
          this.$emit('closeDialog', {
            type: 'success',
            openDialog: false
          })
        } else {
          this.$emit('closeDialog', {
            type: 'error',
            openDialog: false
          })
        }
      },

    }
  };
</script>
