# -*- coding: utf-8 -*-
import re
from django.db import models
from django.core import validators
from django.utils import timezone
from django.core.mail import send_mail
from django.utils.http import urlquote
from django.utils.translation import ugettext_lazy as _
from django.contrib.auth.models import AbstractBaseUser, PermissionsMixin, BaseUserManager
from django.conf import settings

'''class Apartament(models.Model):
	number = models.IntegerField(_('número'), unique=True)
	vacancy = models.IntegerField(_('vagas'))
	capacity = models.IntegerField(_('capacidade'))
	#id_block - COLOCAR A CHAVE ESTRANGEIRA

class Block(models.Model):
	number = models.IntegerField(_('número'), unique=True)
	buildNumber = models.IntegerField(_('número do prédio'), unique=True)'''


class Course(models.Model):
	courseName = models.CharField(_('nome do curso'), max_length=255 ,unique=True)
	duration = models.IntegerField(_('duração'))

	TYPES_OF_GRADUATION = (
		('Grad', 'Graduação'),
		('Tec', 'Ensino Técnico'),
		('Tecnl', 'Tecnólogo'),
	)

	_type = models.CharField(_('tipo'), max_length=5, choices=TYPES_OF_GRADUATION)

	def __str__(self):
		return str(str(self.id) + ' -  ' + (self.courseName))

'''class WaitingList(models.Model):
	id_user = 
	id_apto = '''

class UserManager(BaseUserManager):
	
	def _create_user(self, fullName, email, cpf, rg, age,id_course,registration, phone1, phone2, password, is_staff, is_superuser, **extra_fields):
		now = timezone.now()
		if not registration:
			#raise ValueError(_(‘The given username must be set’))
			print("erro")
		email = self.normalize_email(email)
		user = self.model(fullName=fullName, email=email,cpf=cpf, rg=rg, age=age,id_course=id_course,registration=registration, phone1=phone1, phone2=phone2, is_staff=is_staff, is_active=True, is_superuser=is_superuser, last_login=now, date_joined=now, **extra_fields)
		user.set_password(password)
		user.save(using=self._db)
		return user
	
	def create_user(self, fullName, email, phone1, age, id_course,phone2, rg, cpf, password, is_staff, is_superuser, **extra_fields):
		print(fullName)
		return self._create_user(fullName, email, phone1,age, id_course,phone1, rg, cpf, password, False, False, **extra_fields)
	
	def create_superuser(self, fullName, email, cpf, rg, age,registration, phone1, phone2, password, **extra_fields):

		user=self._create_user(fullName, email, cpf, rg, age,registration, phone1, phone2, password, True, True, **extra_fields)
		user.is_active=True
		user.save(using=self._db)
		return user


class Student(AbstractBaseUser, PermissionsMixin):
	
	fullName = models.CharField(_('Nome Completo'), max_length=255, unique=False, help_text=_('Entre com seu nome.'))
	email = models.EmailField(_('endereço de email'), max_length=255, unique=True)
	cpf = models.CharField(_('cpf'), max_length=15, unique=True)
	rg = models.CharField(_('rg'), max_length=10, unique=True)
	registration = models.CharField(_('matricula'), max_length=9, unique=True)
	phone1 = models.CharField(_('telefone 1'), max_length=13, unique=True)
	phone2 = models.CharField(_('telefone 2'), max_length=13, blank=True)
	age = models.IntegerField(_('idade'))
	is_staff = models.BooleanField(_('staff status'), default=False, help_text=_('Designates whether the user can log into this admin site.'))
	is_active = models.BooleanField(_('active'), default=True, help_text=_('Designates whether this user should be treated as active. Unselect this instead of deleting accounts.'))
	is_bse_active = models.BooleanField(_('bse_active'), default=False)
	date_joined = models.DateTimeField(_('date joined'), default=timezone.now)

	GENDER = (
		('M', 'Masculino'),
		('F', 'Feminino'),
	)

	gender = models.CharField(_('genero'), max_length=1, choices=GENDER)

	id_course = models.ForeignKey(to=Course, to_field="id" ,related_name='course' ,on_delete=models.CASCADE)

	USERNAME_FIELD = 'registration'
	REQUIRED_FIELDS = ['fullName', 'email', 'cpf', 'rg', 'phone1', 'phone2', 'age']

	objects = UserManager()

	class Meta:
		verbose_name = _('student')
		verbose_name_plural = _('students')
	
	def __unicode__(self):
		return '%d: %s' % (self.order, self.id_course)


	'''def get_full_name(self):
		full_name = '%s %s' % (self.first_name, self.last_name)
		return full_name.strip()

	def get_short_name(self):
		return self.first_name

	def email_user(self, subject, message, from_email=None):
		send_mail(subject, message, from_email, [self.email])'''