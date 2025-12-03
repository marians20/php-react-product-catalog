import React, { useState, useEffect } from 'react';
import {
  Container,
  Paper,
  Table,
  TableBody,
  TableCell,
  TableContainer,
  TableHead,
  TableRow,
  Button,
  IconButton,
  Typography,
  Box,
  Chip,
  Alert,
  CircularProgress,
  Switch,
  Tooltip
} from '@mui/material';
import {
  Add as AddIcon,
  Edit as EditIcon,
  Delete as DeleteIcon,
  Block as BlockIcon,
  CheckCircle as CheckCircleIcon
} from '@mui/icons-material';
import { userService } from '../services/api';
import UserDialog from '../components/UserDialog';
import DeleteConfirmDialog from '../components/DeleteConfirmDialog';

const UserList = () => {
  const [users, setUsers] = useState([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState('');
  const [openDialog, setOpenDialog] = useState(false);
  const [openDeleteDialog, setOpenDeleteDialog] = useState(false);
  const [selectedUser, setSelectedUser] = useState(null);
  const [userToDelete, setUserToDelete] = useState(null);

  const fetchUsers = async () => {
    try {
      setLoading(true);
      const data = await userService.getAllUsers();
      setUsers(data);
      setError('');
    } catch (err) {
      setError(err.response?.data?.error || 'Failed to fetch users');
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => {
    fetchUsers();
  }, []);

  const handleOpenDialog = (user = null) => {
    setSelectedUser(user);
    setOpenDialog(true);
  };

  const handleCloseDialog = () => {
    setSelectedUser(null);
    setOpenDialog(false);
  };

  const handleSaveUser = async (userData) => {
    try {
      if (selectedUser) {
        await userService.updateUser(selectedUser.id, userData);
      } else {
        // For new users, use the register endpoint
        await fetch('http://localhost:8000/api/register', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
          },
          body: JSON.stringify(userData),
        });
      }
      
      handleCloseDialog();
      fetchUsers();
    } catch (err) {
      setError(err.response?.data?.error || 'Failed to save user');
    }
  };

  const handleOpenDeleteDialog = (user) => {
    setUserToDelete(user);
    setOpenDeleteDialog(true);
  };

  const handleCloseDeleteDialog = () => {
    setUserToDelete(null);
    setOpenDeleteDialog(false);
  };

  const handleDeleteUser = async () => {
    try {
      await userService.deleteUser(userToDelete.id);
      handleCloseDeleteDialog();
      fetchUsers();
    } catch (err) {
      setError(err.response?.data?.error || 'Failed to delete user');
    }
  };

  const handleToggleStatus = async (user) => {
    try {
      await userService.toggleUserStatus(user.id, !user.enabled);
      fetchUsers();
    } catch (err) {
      setError(err.response?.data?.error || 'Failed to update user status');
    }
  };

  const getRoleColor = (role) => {
    switch (role) {
      case 'ROLE_ADMIN':
        return 'error';
      case 'ROLE_USER':
        return 'primary';
      default:
        return 'default';
    }
  };

  if (loading) {
    return (
      <Box display="flex" justifyContent="center" alignItems="center" minHeight="400px">
        <CircularProgress />
      </Box>
    );
  }

  return (
    <Container maxWidth="lg" sx={{ mt: 4 }}>
      <Box display="flex" justifyContent="space-between" alignItems="center" mb={3}>
        <Typography variant="h4" component="h1">
          User Management
        </Typography>
        <Button
          variant="contained"
          startIcon={<AddIcon />}
          onClick={() => handleOpenDialog()}
        >
          Add User
        </Button>
      </Box>

      {error && (
        <Alert severity="error" sx={{ mb: 2 }} onClose={() => setError('')}>
          {error}
        </Alert>
      )}

      <TableContainer component={Paper}>
        <Table>
          <TableHead>
            <TableRow>
              <TableCell>ID</TableCell>
              <TableCell>Name</TableCell>
              <TableCell>Email</TableCell>
              <TableCell>Roles</TableCell>
              <TableCell>Status</TableCell>
              <TableCell>Created At</TableCell>
              <TableCell align="right">Actions</TableCell>
            </TableRow>
          </TableHead>
          <TableBody>
            {users.length === 0 ? (
              <TableRow>
                <TableCell colSpan={7} align="center">
                  No users found
                </TableCell>
              </TableRow>
            ) : (
              users.map((user) => (
                <TableRow key={user.id}>
                  <TableCell>{user.id}</TableCell>
                  <TableCell>{user.name}</TableCell>
                  <TableCell>{user.email}</TableCell>
                  <TableCell>
                    {user.roles.map((role) => (
                      <Chip
                        key={role}
                        label={role.replace('ROLE_', '')}
                        color={getRoleColor(role)}
                        size="small"
                        sx={{ mr: 0.5 }}
                      />
                    ))}
                  </TableCell>
                  <TableCell>
                    <Box display="flex" alignItems="center">
                      <Tooltip title={user.enabled ? 'Disable user' : 'Enable user'}>
                        <Switch
                          checked={user.enabled}
                          onChange={() => handleToggleStatus(user)}
                          color="primary"
                        />
                      </Tooltip>
                      {user.enabled ? (
                        <CheckCircleIcon color="success" sx={{ ml: 1 }} />
                      ) : (
                        <BlockIcon color="error" sx={{ ml: 1 }} />
                      )}
                    </Box>
                  </TableCell>
                  <TableCell>{user.createdAt}</TableCell>
                  <TableCell align="right">
                    <Tooltip title="Edit user">
                      <IconButton
                        color="primary"
                        onClick={() => handleOpenDialog(user)}
                      >
                        <EditIcon />
                      </IconButton>
                    </Tooltip>
                    <Tooltip title="Delete user">
                      <IconButton
                        color="error"
                        onClick={() => handleOpenDeleteDialog(user)}
                      >
                        <DeleteIcon />
                      </IconButton>
                    </Tooltip>
                  </TableCell>
                </TableRow>
              ))
            )}
          </TableBody>
        </Table>
      </TableContainer>

      <UserDialog
        open={openDialog}
        onClose={handleCloseDialog}
        onSave={handleSaveUser}
        user={selectedUser}
      />

      <DeleteConfirmDialog
        open={openDeleteDialog}
        onClose={handleCloseDeleteDialog}
        onConfirm={handleDeleteUser}
        title="Delete User"
        message={`Are you sure you want to delete user "${userToDelete?.name}"? This action cannot be undone.`}
      />
    </Container>
  );
};

export default UserList;
