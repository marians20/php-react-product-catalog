import React, { useState, useEffect } from 'react';
import {
  Dialog,
  DialogTitle,
  DialogContent,
  DialogActions,
  TextField,
  Button,
  Box,
  Alert,
} from '@mui/material';

const ProductDialog = ({ open, product, onClose, onSave }) => {
  const [formData, setFormData] = useState({
    name: '',
    description: '',
    price: '',
    stock: '',
  });
  const [errors, setErrors] = useState({});
  const [saveError, setSaveError] = useState(null);

  useEffect(() => {
    if (product) {
      setFormData({
        name: product.name || '',
        description: product.description || '',
        price: product.price || '',
        stock: product.stock || '',
      });
    } else {
      setFormData({
        name: '',
        description: '',
        price: '',
        stock: '',
      });
    }
    setErrors({});
    setSaveError(null);
  }, [product, open]);

  const validate = () => {
    const newErrors = {};

    if (!formData.name.trim()) {
      newErrors.name = 'Name is required';
    }

    if (!formData.description.trim()) {
      newErrors.description = 'Description is required';
    }

    const price = parseFloat(formData.price);
    if (!formData.price || isNaN(price) || price < 0) {
      newErrors.price = 'Valid price is required (must be >= 0)';
    }

    const stock = parseInt(formData.stock, 10);
    if (!formData.stock || isNaN(stock) || stock < 0) {
      newErrors.stock = 'Valid stock is required (must be >= 0)';
    }

    setErrors(newErrors);
    return Object.keys(newErrors).length === 0;
  };

  const handleChange = (e) => {
    const { name, value } = e.target;
    setFormData((prev) => ({
      ...prev,
      [name]: value,
    }));
    // Clear error for this field
    if (errors[name]) {
      setErrors((prev) => ({
        ...prev,
        [name]: '',
      }));
    }
  };

  const handleSubmit = async (e) => {
    e.preventDefault();
    setSaveError(null);

    if (!validate()) {
      return;
    }

    try {
      const dataToSave = {
        name: formData.name.trim(),
        description: formData.description.trim(),
        price: parseFloat(formData.price),
        stock: parseInt(formData.stock, 10),
      };

      await onSave(dataToSave);
    } catch (err) {
      setSaveError(err.response?.data?.error || 'Failed to save product');
    }
  };

  return (
    <Dialog open={open} onClose={onClose} maxWidth="sm" fullWidth>
      <form onSubmit={handleSubmit}>
        <DialogTitle>
          {product ? 'Edit Product' : 'Add New Product'}
        </DialogTitle>
        <DialogContent>
          <Box sx={{ pt: 1 }}>
            {saveError && (
              <Alert severity="error" sx={{ mb: 2 }}>
                {saveError}
              </Alert>
            )}

            <TextField
              autoFocus
              margin="dense"
              name="name"
              label="Product Name"
              type="text"
              fullWidth
              variant="outlined"
              value={formData.name}
              onChange={handleChange}
              error={!!errors.name}
              helperText={errors.name}
              required
            />

            <TextField
              margin="dense"
              name="description"
              label="Description"
              type="text"
              fullWidth
              variant="outlined"
              multiline
              rows={3}
              value={formData.description}
              onChange={handleChange}
              error={!!errors.description}
              helperText={errors.description}
              required
            />

            <TextField
              margin="dense"
              name="price"
              label="Price"
              type="number"
              fullWidth
              variant="outlined"
              value={formData.price}
              onChange={handleChange}
              error={!!errors.price}
              helperText={errors.price}
              inputProps={{ min: 0, step: 0.01 }}
              required
            />

            <TextField
              margin="dense"
              name="stock"
              label="Stock"
              type="number"
              fullWidth
              variant="outlined"
              value={formData.stock}
              onChange={handleChange}
              error={!!errors.stock}
              helperText={errors.stock}
              inputProps={{ min: 0, step: 1 }}
              required
            />
          </Box>
        </DialogContent>
        <DialogActions>
          <Button onClick={onClose}>Cancel</Button>
          <Button type="submit" variant="contained">
            {product ? 'Update' : 'Create'}
          </Button>
        </DialogActions>
      </form>
    </Dialog>
  );
};

export default ProductDialog;
