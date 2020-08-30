<template>
  <el-dialog title="修改分类" :visible.sync="dialogVisible" @close="dialogFormVisible" :close-on-click-modal="false" :close-on-press-escape="false">
    <el-form size="small" :model="form" :rules="formRules" ref="form">
      <el-form-item label="分类名称" prop="name" :label-width="formLabelWidth">
        <el-input v-model="form.name" autocomplete="off"></el-input>
      </el-form-item>
      <el-form-item label="分类排序" prop="sort" :label-width="formLabelWidth"><el-input v-model.number="form.sort" autocomplete="off"></el-input></el-form-item>
    </el-form>

    <div slot="footer" class="dialog-footer">
      <el-button @click="dialogFormVisible">取 消</el-button>
      <el-button type="primary" @click="addUser" :loading="loading">确 定</el-button>
    </div>
  </el-dialog>
</template>

<script>
import CasesApi from '@/api/cases.js';
import Upload from '@/components/file/Upload';
export default {
  components: {
    Upload
  },
  data() {
    return {
       /*表单数据对象*/
      form: {
        cid: 0,
        name: '',
        sort: ''
      },
      file_path: '',
      /*验证规则*/
      formRules: {
        name: [
          {
            required: true,
            message: '请输入分类名称',
            trigger: 'blur'
          }
        ],
        sort: [
          {
            required: true,
            message: '分类排序不能为空'
          },
          {
            type: 'number',
            message: '分类排序必须为数字'
          }
        ]
      },
      /*左边长度*/
      formLabelWidth: '120px',
      /*是否显示*/
      dialogVisible: false,
       /*是否加载完成*/
      loading: false
    };
  },
  props: ['open_edit', 'editform'],
  created() {
    this.dialogVisible = this.open_edit;
    this.form.cid = this.editform.model.cid;
    this.form.parent_id = this.editform.model.parent_id;
    this.form.name = this.editform.model.name;
    this.form.sort = this.editform.model.sort;
    this.getImage(this.editform.model.cid);
  },
  methods: {
    /*修改用户*/
    addUser() {
      let self = this;
      let params = self.form;
      self.$refs.form.validate(valid => {
        if (valid) {
          self.loading = true;
          CasesApi.catEdit(params, true)
            .then(data => {
              self.loading = false;
              self.$message({
                message: '修改成功',
                type: 'success'
              });
              self.dialogFormVisible(true);
            })
            .catch(error => {
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
        });
      } else {
        this.$emit('closeDialog', {
          type: 'error',
          openDialog: false
        });
      }
    }
  }
};
</script>
