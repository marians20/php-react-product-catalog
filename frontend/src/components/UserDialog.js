import React, { useState, useEffect } from 'react';
import {
  Dialog,
  DialogTitle,
  DialogContent,
  DialogActions,
  TextField,
  Button,
  FormControl,
  InputLabel,
  Select,
  MenuItem,
  OutlinedInput,
  Chip,
  Box
} from '@mui/material';

const ROLES = ['ROLE_USER', 'ROLE_ADMIN'];

const UserDialog = ({ open, onClose, onSave, user }) => {
  const [formData, setFormData] = useState({
    name: '',
    email: '',
    password: '',
    roles: ['ROLE_USER']
  });

  useEffect(() => {
    if (user) {
      setFormData({
        name: user.name || '',
        email: user.email || '',
        password: '',
        roles: user.roles || ['ROLE_USER']
      });
    } else {
      setFormData({
        name: '',
        email: '',
        password: '',
        roles: ['ROLE_USER']
      });
    }
  }, [user, open]);

  const handleChange = (e) => {
    setFormData({
      ...formData,
      [e.target.name]: e.target.value
    });
  };

  const handleRoleChange = (event) => {
    const {
      target: { value },
    } = event;
    setFormData({
      ...formData,
      roles: typeof value === 'string' ? value.split(',') : value
    });
  };

  const handleSubmit = (e) => {
    e.preventDefault();
    
    // Only send fields that have values
    const dataToSend = { ...formData };
    if (user && !dataToSend.password) {
      delete dataToSend.password; // Don't send empty password on update
    }
    
    onSave(dataToSend);
  };

  return (
    <Dialog open={open} onClose={onClose} maxWidth="sm" fullWidth>
      <form onSubmit={handleSubmit}>
        <DialogTitle>{user ? 'Edit User' : 'Add New User'}</DialogTitle>
        <DialogContent>
          <TextField
            fullWidth
            label="Name"
            name="name"
            value={formData.name}
            onChange={handleChange}
            margin="normal"
            required
          />
          <TextField
            fullWidth
            label="Email"
            name="email"
            type="email"
            value={formData.email}
            onChange={handleChange}
            margin="normal"
            required
          />
          <TextField
            fullWidth
            label={user ? 'Password (leave empty to keep current)' : 'Password'}
            name="password"
            type="password"
            value={formData.password}
            onChange={handleChange}
            margin="normal"
            required={!user}
          />
          <FormControl fullWidth margin="normal">
            <InputLabel>Roles</InputLabel>
            <Select
              multiple
              value={formData.roles}
              onChange={handleRoleChange}
              input={<OutlinedInput label="Roles" />}
              renderValue={(selected) => (
                <Box sx={{ display: 'flex', flexWrap: 'wrap', gap: 0.5 }}>
                  {selected.map((value) => (
                    <Chip key={value} label={value} size="small" />
                  ))}
                </Box>
              )}
            >
              {ROLES.map((role) => (
                <MenuItem key={role} value={role}>
                  {role}
                </MenuItem>
              ))}
            </Select>
          </FormControl>
        </DialogContent>
        <DialogActions>
          <Button onClick={onClose}>Cancel</Button>
          <Button type="submit" variant="contained">
            {user ? 'Update' : 'Create'}
          </Button>
        </DialogActions>
      </form>
    </Dialog>
  );
};

export default UserDialog;
