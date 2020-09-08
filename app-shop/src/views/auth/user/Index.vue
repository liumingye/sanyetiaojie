<template>
  <div class="user">
    <!--添加人员-->
    <div class="common-level-rail">
      <el-button size="small" type="primary" icon="el-icon-plus" @click="addClick" v-auth="'/auth/user/add'">添加人员</el-button>
    </div>

    <!--内容-->
    <div class="product-content">
      <div class="table-wrap">
        <el-table size="small" :data="tableData" style="width: 100%" v-loading="loading">
          <el-table-column prop="shop_user_id" label="人员ID"></el-table-column>
          <el-table-column prop="user_name" label="用户名"></el-table-column>
          <el-table-column prop="real_name" label="姓名"></el-table-column>
          <el-table-column prop="role.role_name" label="所属角色">
            <template slot-scope="scope">
              <el-tag size="medium">
              <div v-if="scope.row.role == 0">
                超级管理员
              </div>
              <div v-if="scope.row.role == 1">
                委员会
              </div>
              <div v-if="scope.row.role == 2">
                律师
              </div>
              </el-tag>
            </template>
          </el-table-column>
          <el-table-column label="所属分组">
            <template slot-scope="scope">
              {{ groupText(scope.row.gid) }}
            </template>
          </el-table-column>
          <el-table-column prop="create_time" label="添加时间"></el-table-column>
          <el-table-column fixed="right" align="right" width="150px">
            <template slot-scope="scope">
              <el-button v-if="scope.row.shop_user_id != 1" @click="editClick(scope.row)" size="mini" v-auth="'/auth/user/edit'">编辑</el-button>
              <el-button v-if="scope.row.shop_user_id != 1" @click="deleteClick(scope.row)" size="mini" type="danger" v-auth="'/auth/user/delete'">删除</el-button>
            </template>
          </el-table-column>
        </el-table>
      </div>

      <!--分页-->
      <div class="pagination">
        <el-pagination @size-change="handleSizeChange" @current-change="handleCurrentChange" background :current-page="curPage" :page-size="pageSize" layout="total, prev, pager, next, jumper" :total="totalDataNumber"></el-pagination>
      </div>
    </div>

    <Add :open="open_add" @close="closeAdd"></Add>

    <Edit :open="open_edit" :shop_user_id="curModel.shop_user_id" @close="closeEdit"></Edit>

  </div>
</template>

<script>
  import AuthApi from '@/api/auth.js';
  import Add from './dialog/add.vue';
  import Edit from './dialog/edit.vue';
  export default {
    components: {
      Add,
      Edit
    },
    inject: ['reload'],
    data() {
      return {
        /*是否加载完成*/
        loading: true,
        /*列表数据*/
        tableData: [],
        /*一页多少条*/
        pageSize: 20,
        /*一共多少条数据*/
        totalDataNumber: 0,
        /*当前是第几页*/
        curPage: 1,
        /*横向表单数据模型*/
        formInline: {
          user: '',
          region: ''
        },
        /*是否打开添加弹窗*/
        open_add: false,
        /*是否打开编辑弹窗*/
        open_edit: false,
        /*当前编辑的对象*/
        curModel: {},
        /*角色列表*/
        roleList: [],
        groupList: []
      };
    },
    created() {
      /*获取列表*/
      this.getBaseData();
      this.getTableList();
    },
    methods: {
      groupText: function (id) {
        for (var i = 0; i < this.groupList.length; i++) {
          if (this.groupList[i].id == id) {
            return this.groupList[i].text;
          }
        }
      },
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
      /*选择第几页*/
      handleCurrentChange(val) {
        let self = this;
        self.curPage = val;
        self.loading = true;
        self.getTableList();
      },

      /*每页多少条*/
      handleSizeChange(val) {
        this.curPage = 1;
        this.pageSize = val;
        this.getTableList();
      },

      /*获取列表*/
      getTableList() {
        let self = this;
        let Params = {};
        Params.page = self.curPage;
        Params.list_rows = self.pageSize;
        AuthApi.userList(Params, true)
          .then(data => {
            self.loading = false;
            self.tableData = data.data.list.data;
            self.totalDataNumber = data.data.list.total;
          })
          .catch(error => {});
      },

      /*打开添加*/
      addClick() {
        this.open_add = true;
      },

      /*关闭添加*/
      closeAdd(e) {
        this.open_add = false;
        if (e.type == 'success') {
          this.getTableList();
        }
      },

      /*打开编辑*/
      editClick(row) {
        this.curModel = row;
        this.open_edit = true;
      },

      /*关闭添加*/
      closeEdit(e) {
        this.open_edit = false;
        if (e.type == 'success') {
          this.getTableList();
        }
      },

      /*删除*/
      deleteClick(row) {
        let self = this;
        self
          .$confirm('此操作将永久删除该记录, 是否继续?', '提示', {
            confirmButtonText: '确定',
            cancelButtonText: '取消',
            type: 'warning'
          })
          .then(() => {
            self.loading = true;
            AuthApi.userDelete({
                  shop_user_id: row.shop_user_id
                },
                true
              )
              .then(data => {
                self.loading = false;
                if (data.code == 1) {
                  self.$message({
                    message: '恭喜你，该管理员删除成功',
                    type: 'success'
                  });
                  //刷新页面
                  self.getTableList();
                } else {
                  self.loading = false;
                }
              })
              .catch(error => {
                self.loading = false;
              });
          })
          .catch(() => {});
      }
    }
  };

</script>
