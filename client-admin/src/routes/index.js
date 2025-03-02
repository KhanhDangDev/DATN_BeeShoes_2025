import { Suspense, lazy } from 'react';
import { Navigate, useRoutes, useLocation } from 'react-router-dom';
import LoadingScreen from '../components/LoadingScreen';
import DashboardLayout from '../layouts';

// ----------------------------------------------------------------------

const Loadable = (Component) => (props) => {
  // eslint-disable-next-line react-hooks/rules-of-hooks
  window.scrollTo(0, 0);
  return (
    <Suspense fallback={<LoadingScreen />}>
      <Component {...props} />
    </Suspense>
  );
};

export default function Router() {
  return useRoutes([
    {
      path: '/',
      element: (
        <DashboardLayout />
      ),
      children: [
        { path: 'thong-ke', element: <ThongKe /> },
        {
          path: 'san-pham',
          children: [
            { path: 'danh-sach', element: <DanhSachSanPham /> },
            { path: 'tao-moi', element: <ThemSuaSanPham /> },
            { path: ':id', element: <ThemSuaSanPham /> },
          ],
        },

        {
          path: 'danh-muc',
          children: [
            { path: 'danh-sach', element: <DanhSachDanhMuc /> },
            { path: 'tao-moi', element: <ThemSuaDanhMuc /> },
            { path: ':id', element: <ThemSuaDanhMuc /> },
          ],
        },

        {
          path: 'mau-sac',
          children: [
            { path: 'danh-sach', element: <DanhSachMauSac /> },
            { path: 'tao-moi', element: <ThemSuaMauSac /> },
            { path: ':id', element: <ThemSuaMauSac /> },
          ],
        },

        {
          path: 'chat-lieu',
          children: [
            { path: 'danh-sach', element: <DanhSachMauSac /> },
            { path: 'tao-moi', element: <ThemSuaMauSac /> },
            { path: ':id', element: <ThemSuaMauSac /> },
          ],
        },

        {
          path: 'don-hang',
          children: [
            { path: 'danh-sach', element: <DanhSachDonHang /> },
            { path: ':id', element: <DonHangChiTiet /> },
          ],
        },

        {
          path: 'nhan-vien',
          children: [
            { path: 'danh-sach', element: <DanhSachNhanVien /> },
            { path: 'tao-moi', element: <ThemSuaNhanVien /> },
            { path: ':id', element: <ThemSuaNhanVien /> },
          ],
        },

        {
          path: 'khach-hang',
          children: [
            { path: 'danh-sach', element: <DanhSachKhachHang /> },
            { path: 'tao-moi', element: <ThemSuaKhachHang /> },
            { path: ':id', element: <ThemSuaKhachHang /> },
          ],
        },

        {
          path: 'voucher',
          children: [
            { path: 'danh-sach', element: <DanhSachVoucher /> },
            { path: 'tao-moi', element: <ThemSuaVoucher /> },
            { path: ':id', element: <ThemSuaVoucher /> },
          ],
        },
      ],
    },

    // { path: '/', element: <Navigate to="/dashboard/employee/list" replace /> },
    // { path: '*', element: <Navigate to="/404" replace /> },
  ]);
}

const DanhSachDonHang = Loadable(lazy(() => import('../pages/dashboard/don-hang/DanhSachDonHang')));
const DonHangChiTiet = Loadable(lazy(() => import('../pages/dashboard/don-hang/DonHangChiTiet')));

const DanhSachSanPham = Loadable(lazy(() => import('../pages/dashboard/san-pham/DanhSachSanPham')));
const ThemSuaSanPham = Loadable(lazy(() => import('../pages/dashboard/san-pham/ThemSuaSanPham')));

const DanhSachThuongHieu = Loadable(lazy(() => import('../pages/dashboard/san-pham/DanhSachThuongHieu')));
const ThemSuaThuongHieu = Loadable(lazy(() => import('../pages/dashboard/san-pham/ThemSuaThuongHieu')));

const DanhSachMauSac = Loadable(lazy(() => import('../pages/dashboard/san-pham/DanhSachMauSac')));
const ThemSuaMauSac = Loadable(lazy(() => import('../pages/dashboard/san-pham/ThemSuaMauSac')));

const ThemSuaNhanVien = Loadable(lazy(() => import('../pages/dashboard/nhan-vien/ThemSuaNhanVien')));
const DanhSachNhanVien = Loadable(lazy(() => import('../pages/dashboard/nhan-vien/DachSachNhanVien')));

const ThemSuaKhachHang = Loadable(lazy(() => import('../pages/dashboard/khach-hang/ThemSuaKhachHang')));
const DanhSachKhachHang = Loadable(lazy(() => import('../pages/dashboard/khach-hang/DanhSachKhachHang')));

const ThongKe = Loadable(lazy(() => import('../pages/dashboard/thong-ke/ThongKe')));

const DanhSachVoucher = Loadable(lazy(() => import('../pages/dashboard/voucher/DanhSachVoucher')));
const ThemSuaVoucher = Loadable(lazy(() => import('../pages/dashboard/voucher/ThemSuaVoucher')));

const DanhSachDanhMuc = Loadable(lazy(() => import('../pages/dashboard/danh-muc/DanhSachDanhMuc')));
const ThemSuaDanhMuc = Loadable(lazy(() => import('../pages/dashboard/danh-muc/ThemSuaDanhMuc')));
