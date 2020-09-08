<template>
  <div>
    <!--搜索表单-->
    <div class="common-seach-wrap">
      <el-form size="small" :inline="true" :model="searchForm">
        <el-form-item>
          <el-input size="small" v-model="searchForm.search" placeholder="请输入用户名"></el-input>
        </el-form-item>
        <el-form-item>
          <el-button size="small" type="primary" icon="el-icon-search" @click="searchSubmit">查询</el-button>
          <el-button size="small" type="danger" icon="el-icon-delete" @click="empty">清空</el-button>
        </el-form-item>
      </el-form>
    </div>
    <!--内容-->
    <div class="product-content">
      <div class="table-wrap">
        <el-table size="small" :data="tableData" style="width: 100%" v-loading="loading">
          <el-table-column prop="id" label="ID"></el-table-column>
          <el-table-column prop="ip" label="IP"></el-table-column>
          <el-table-column prop="result" label="登录状态"></el-table-column>
          <el-table-column prop="username" label="用户名"></el-table-column>
          <el-table-column prop="create_time" label="添加时间"></el-table-column>
        </el-table>
      </div>
      <!--分页-->
      <div class="pagination">
        <el-pagination @size-change="handleSizeChange" @current-change="handleCurrentChange" background :current-page="curPage" :page-size="pageSize" layout="total, prev, pager, next, jumper" :total="totalDataNumber"></el-pagination>
      </div>
    </div>
  </div>
</template>

<script>
  import AuthApi from '@/api/auth.js';
  export default {
    components: {},
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
        searchForm: {
          search: ''
        },
      };
    },
    created() {
      /*获取列表*/
      this.getTableList();
    },
    methods: {

      /*搜索*/
      searchSubmit() {
        this.curPage = 1;
        this.getTableList();
      },

      /*选择第几页*/
      handleCurrentChange(val) {
        this.curPage = val;
        this.getTableList();
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
        self.loading = true;
        let Params = {
          page: self.curPage,
          list_rows: self.pageSize,
          username: self.searchForm.search
        };

        AuthApi.loginlog(Params, true)
          .then(data => {
            self.loading = false;
            self.tableData = data.data.list.data;
            self.totalDataNumber = data.data.list.total;
          })
          .catch(error => {});
      },

      empty() {
        let self = this;
        self.loading = true;
        AuthApi.loginlogEmpty({}, true)
          .then(data => {
            self.getTableList();
            self.loading = false;
          })
          .catch(error => {});
      }
    }
  };

</script>
