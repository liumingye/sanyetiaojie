<template>
  <div class="law">
    <!--搜索表单-->
    <div class="common-seach-wrap">
      <el-form size="small" :inline="true" :model="formInline" class="demo-form-inline">
        <el-form-item label="标题"><el-input v-model="formInline.text" placeholder="请输入标题" clearable></el-input></el-form-item>
        <el-form-item><el-button type="primary" @click="onSubmit" icon="el-icon-search">查询</el-button></el-form-item>
        <el-form-item><el-button size="small" type="primary" icon="el-icon-plus" @click="addClick" v-auth="'/support/law/add'">添加法规</el-button></el-form-item>
      </el-form>
    </div>
    <!--内容-->
    <div class="content">
      <div class="table-wrap">
        <el-table :data="tableData" size="small" style="width: 100%" v-loading="loading">
          <el-table-column prop="id" label="ID" width="100"></el-table-column>
          <el-table-column prop="title" label="标题"></el-table-column>
          <el-table-column prop="category_name" label="分类"></el-table-column>
          <el-table-column prop="create_time" label="添加时间"></el-table-column>
          <el-table-column fixed="right" align="right" width="150px">
            <template slot-scope="scope">
              <el-button @click="editClick(scope.row)" size="mini" v-auth="'/support/law/edit'">编辑</el-button>
              <el-button @click="deleteClick(scope.row)" size="mini" type="danger" v-auth="'/support/law/delete'">删除</el-button>
            </template>
          </el-table-column>
        </el-table>
      </div>

      <!--分页-->
      <div class="pagination">
        <el-pagination
          @size-change="handleSizeChange"
          @current-change="handleCurrentChange"
          background
          :current-page="curPage"
          :page-size="pageSize"
          layout="total, prev, pager, next, jumper"
          :total="totalDataNumber"
        ></el-pagination>
      </div>
    </div>

  </div>
</template>

<script>
import LawApi from '@/api/law.js';
export default {
  components: {},
  data() {
    return {
      /*是否加载完成*/
      loading: true,
      /*列表数据*/
      tableData: [],
      /*一页多少条*/
      pageSize: 10,
      /*一共多少条数据*/
      totalDataNumber: 0,
      /*当前是第几页*/
      curPage: 1,
      /*横向表单数据模型*/
      formInline: {
        text: ''
      }
    };
  },
  created() {
    /*获取列表*/
    this.getTableList();
  },
  methods: {
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
      self.getData();
    },
    
    getData(){
      let self = this;
      let Params = self.formInline;
      Params.page = self.curPage;
      Params.list_rows = self.pageSize;
      LawApi.supportLists(Params, true)
        .then(data => {
          self.loading = false;
          self.tableData = data.data.data;
          self.totalDataNumber = data.data.total;
        })
        .catch(error => {
          self.loading = false;
        });
    },

    /*打开添加*/
    addClick(item) {
      this.$router.push('/support/law/add');
    },

    /*打开编辑*/
    editClick(item) {
      this.$router.push({
        path: '/support/law/edit',
        query: {
            id: item.id
        }
      });
    },

    /*搜索查询*/
    onSubmit() {
      let self = this;
      self.loading = true;
      let Params = self.formInline;
      self.getTableList();
    },

    /*删除法规*/
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
          LawApi.delLaw(
            {
              id: row.id
            },
            true
          )
            .then(data => {
              self.loading = false;
              if (data.code == 1) {
                self.$message({
                  message: '恭喜你，删除成功',
                  type: 'success'
                });
                self.getTableList();
              } else {
                self.loading = false;
              }
            })
            .catch(error => {
              self.loading = false;
            });
        })
        .catch(() => {
          self.loading = false;
        });
    },
  }
};
</script>
