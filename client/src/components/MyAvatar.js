// hooks
import useAuth from '../hooks/useAuth';
// utils
import createAvatar from '../utils/createAvatar';
//
import Avatar from './Avatar';

// ----------------------------------------------------------------------

export default function MyAvatar({ ...other }) {
  const { user } = useAuth();

  return (
    <Avatar
      src={user?.photoURL}
      alt={user?.fullName}
      color={user?.photoURL ? 'default' : createAvatar(user?.fullName).color}
      {...other}
    >
      {createAvatar(user?.fullName).name}
    </Avatar>
  );
}
