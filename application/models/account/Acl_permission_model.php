<?php
/**
 * A3M (Account Authentication & Authorization) is a CodeIgniter 3.x package.
 * It gives you the CRUD to get working right away without too much fuss and tinkering!
 * Designed for building webapps from scratch without all that tiresome login / logout / admin stuff thats always required.
 *
 * @link https://github.com/donjakobo/A3M GitHub repository
 */
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Acl_permissions_model
 *
 * Model for the Acl_permission table.
 * Holds permissions information.
 *
 * @package A3M
 * @subpackage Models
 */
class Acl_permission_model extends CI_Model
{

  /**
   * Get all permissions, used for Admin Panel
   *
   * @access public
   * @return object all permission details
   */
  function get()
  {
    return $this->db->get($this->db->dbprefix . 'a3m_acl_permission')->result();
  }

  // --------------------------------------------------------------------

  /**
   * Get a single permission by id
   *
   * @access public
   * @param int $id
   * @return object permission details
   */
  function get_by_id($id)
  {
    return $this->db->get_where($this->db->dbprefix . 'a3m_acl_permission', array('id' => $id))->row();
  }

  // --------------------------------------------------------------------

  /**
   * Get active permissions associated with an account
   *
   * @access public
   * @param string $account_id
   * @return object account permissions
   */
  function get_by_account_id($account_id)
  {
    $this->db->select($this->db->dbprefix . 'a3m_acl_permission.*');
    $this->db->from($this->db->dbprefix . 'a3m_acl_permission');
    $this->db->join($this->db->dbprefix . 'a3m_rel_role_permission', $this->db->dbprefix . 'a3m_acl_permission.id = '.$this->db->dbprefix . 'a3m_rel_role_permission.permission_id');
    $this->db->join($this->db->dbprefix . 'a3m_rel_account_role', $this->db->dbprefix . 'a3m_rel_role_permission.role_id = '.$this->db->dbprefix . 'a3m_rel_account_role.role_id');
    $this->db->where($this->db->dbprefix . "a3m_rel_account_role.account_id = $account_id AND ". $this->db->dbprefix . "a3m_acl_permission.suspendedon IS NULL");
    
    return $this->db->get()->result();
  }

  // --------------------------------------------------------------------

  /**
   * Get permission by name
   * @param string $permission_name
   * @access public
   * 
   * @return object permission details
   */
  function get_by_name($permission_name)
  {
    return $this->db->get_where($this->db->dbprefix . 'a3m_acl_permission', array('key' => $permission_name))->row();
  }

  // --------------------------------------------------------------------

  /**
   * Check if the account has a specific permission
   *
   * @access public
   * @param string $permission_key
   * @param string $account_id
   * @return object account permissions
   */
  function has_permission($permission_key, $account_id)
  {
    $this->db->select($this->db->dbprefix . 'a3m_acl_permission.*');
    $this->db->from($this->db->dbprefix . 'a3m_acl_permission');
    $this->db->join($this->db->dbprefix . 'a3m_rel_role_permission', $this->db->dbprefix . 'a3m_acl_permission.id = '.$this->db->dbprefix . 'a3m_rel_role_permission.permission_id');
    $this->db->join($this->db->dbprefix . 'a3m_rel_account_role', $this->db->dbprefix . 'a3m_rel_role_permission.role_id = '.$this->db->dbprefix . 'a3m_rel_account_role.role_id');
    $this->db->where($this->db->dbprefix . "a3m_rel_account_role.account_id = $account_id AND a3m_acl_permission.suspendedon IS NULL AND a3m_acl_permission.key = $permission_key");

    return ($this->db->count_all_results() > 0);
  }

  // --------------------------------------------------------------------
  
  /**
   * Update permission details
   *
   * @access public
   * @param int $permission_id
   * @param array $attributes
   * @return integer permission id
   */
  function update($permission_id, $attributes = array())
  {
    // Update
    if ($this->get_by_id($permission_id))
    {
      $this->db->where('id', $permission_id);
      $this->db->update($this->db->dbprefix . 'a3m_acl_permission', $attributes);
    }
    // Insert
    else
    {
      $this->db->insert($this->db->dbprefix . 'a3m_acl_permission', $attributes);
      $permission_id = $this->db->insert_id();
    }

    return $permission_id;
  }

  // --------------------------------------------------------------------

  /**
   * Update permission suspended datetime
   *
   * @access public
   * @param int $permission_id
   * @return void
   */
  function update_suspended_datetime($permission_id)
  {
    $this->load->helper('date');

    $this->db->update($this->db->dbprefix . 'a3m_acl_permission', array('suspendedon' => mdate('%Y-%m-%d %H:%i:%s', now())), array('id' => $permission_id));
  }

  // --------------------------------------------------------------------
  
  /**
   * Remove permission suspended datetime
   *
   * @access public
   * @param int $permission_id
   * @return void
   */
  function remove_suspended_datetime($permission_id)
  {
    $this->db->update($this->db->dbprefix . 'a3m_acl_permission', array('suspendedon' => NULL), array('id' => $permission_id));
  }

  // --------------------------------------------------------------------

  /**
   * Delete permission details
   *
   * @access public
   * @param int $permission_id
   * @return void
   */
  function delete($permission_id)
  {
    $this->db->delete($this->db->dbprefix . 'a3m_acl_permission', array('id' => $permission_id));
  }
}
/* End of file Acl_permission_model.php */
/* Location: ./application/models/account/Acl_permission_model.php */