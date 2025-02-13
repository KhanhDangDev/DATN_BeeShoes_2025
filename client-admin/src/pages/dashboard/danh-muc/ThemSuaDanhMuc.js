import { useState, useEffect } from "react";
import axios from 'axios';
import { Link, useParams } from "react-router-dom";
// routes
import { DUONG_DAN_TRANG } from "../../../routes/duong-dan";
// components
import Page from '../../../components/Page';
import Container from '../../../components/Container';
import { HeaderBreadcrumbs } from '../../../components/HeaderSection';
import FormThemSuaDanhMuc from './FormThemSuaDanhMuc';

// hooks
import useLoading from '../../../hooks/useLoading';


export default function ThemSuaDanhMuc() {
  const { id } = useParams();

  const { onOpenLoading, onCloseLoading } = useLoading();
  const [data, setData] = useState([]);

  useEffect(() => {
    const layDuLieuTuBackEnd = async () => {
      // bật loading
      onOpenLoading();
      try {
        // gọi api từ backend
        const response = await axios.get(`http://127.0.0.1:8000/api/thuong-hieu/${id}`);

        // nếu gọi api thành công sẽ set dữ liệu
        setData(response.data.data); // set dữ liệu được trả về từ backend
        console.log(response.data.data);
      } catch (error) {
        console.error(error);
        // console ra lỗi
      } finally {
        onCloseLoading();
        // tắt loading
      }
    }

    if (id) {
      // nếu là giao diện cập nhật => gọi hàm lấy dữ liệu
      layDuLieuTuBackEnd();
    }
  }, [id]) // hàm sẽ được gọi khi các biến này được thay đổi => id trên đường dẫn thay đổi

  return (
    <>
      <Page title={id ? "Cập nhật danh mục" : "Thêm mới danh mục"}>
        <Container>
          <HeaderBreadcrumbs
            heading={id ? "Cập nhật danh mục" : "Thêm mới danh mục"}
            links={[
              {
                title: <Link to={DUONG_DAN_TRANG.san_pham.thuong_hieu}>Danh sách danh mục</Link>,
              },
              {
                title: id ? "Cập nhật danh mục" : "Thêm mới danh mục",
              },
            ]}
          />

          <FormThemSuaDanhMuc
            laCapNhat={id ? true : false}
            thuongHieuHienTai={data}
          />
        </Container>

      </Page>
    </>
  )

}
