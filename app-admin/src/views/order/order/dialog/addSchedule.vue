<template>
  <el-dialog title="添加进度" :visible.sync="dialogVisible" @close="dialogFormVisible" :close-on-click-modal="false" :close-on-press-escape="false" width="50%">
    <el-form size="small" :model="form" ref="form">
      <el-form-item label="文本描述" :label-width="formLabelWidth" prop="text" :rules="[{required: true,message: ' '}]">
        <el-input type="textarea" :autosize="{ minRows: 4}" v-model="form.text" autocomplete="off"></el-input>
      </el-form-item>
      <el-form-item label="进度状态" :label-width="formLabelWidth" prop="status" :rules="[{required: true,message: ' '}]">
        <el-select v-model="form.status" placeholder="请选择">
          <el-option v-for="item in options" :key="item.value" :label="item.label" :value="item.value">
          </el-option>
        </el-select>
      </el-form-item>
      <el-form-item label="进度时间" :label-width="formLabelWidth" prop="time" :rules="[{required: true,message: ' '}]">
        <el-date-picker v-model="form.time" type="datetime" placeholder="选择进度时间">
        </el-date-picker>
      </el-form-item>
      <el-form-item :label-width="formLabelWidth" prop="sendNotice">
        <el-checkbox v-model="form.sendNotice">自动发送一条消息</el-checkbox>
      </el-form-item>
    </el-form>
    <div slot="footer" class="dialog-footer">
      <el-button @click="dialogFormVisible(false)">取 消</el-button>
      <el-button type="primary" @click="submitFunc()" :loading="loading">确 定</el-button>
    </div>
  </el-dialog>
</template>

<script>
  import OrderApi from '@/api/order.js';
  export default {
    data() {
      return {
        options: [{
          value: 1,
          label: '待调解'
        }, {
          value: 2,
          label: '调解中'
        }, {
          value: 3,
          label: '调解成功'
        }, {
          value: 4,
          label: '调解失败'
        }],
        loading: false,
        /*左边长度*/
        formLabelWidth: '100px',
        /*是否显示*/
        dialogVisible: true,
        /*表单*/
        form: {
          id: 0,
          text: '',
          status: 2,
          time: new Date(),
          sendNotice: true
        }
      };
    },
    created() {
      this.form.id = this.$route.query.id;
    },
    methods: {
      /*确认事件*/
      submitFunc(e) {
        let self = this;
        let form = this.form;
        self.$refs.form.validate((valid) => {
          if (valid) {
            self.loading = true;
            OrderApi.addSchedule(self.form, true).then(data => {
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
          this.$emit('close', {
            type: 'success',
            openDialog: false
          })
        } else {
          this.$emit('close', {
            type: 'error',
            openDialog: false
          })
        }
      }
    }
  };

</script>
