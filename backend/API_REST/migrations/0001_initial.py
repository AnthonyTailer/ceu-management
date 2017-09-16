# -*- coding: utf-8 -*-
# Generated by Django 1.11.5 on 2017-09-14 18:54
from __future__ import unicode_literals

from django.db import migrations, models
import django.db.models.deletion
import django.utils.timezone


class Migration(migrations.Migration):

    initial = True

    dependencies = [
        ('auth', '0008_alter_user_username_max_length'),
    ]

    operations = [
        migrations.CreateModel(
            name='Course',
            fields=[
                ('id', models.AutoField(auto_created=True, primary_key=True, serialize=False, verbose_name='ID')),
                ('courseName', models.CharField(max_length=255, unique=True, verbose_name='nome do curso')),
                ('duration', models.IntegerField(verbose_name='duração')),
                ('_type', models.CharField(choices=[('Grad', 'Graduação'), ('Tec', 'Ensino Técnico'), ('Tecnl', 'Tecnólogo')], max_length=5, verbose_name='tipo')),
            ],
        ),
        migrations.CreateModel(
            name='Student',
            fields=[
                ('id', models.AutoField(auto_created=True, primary_key=True, serialize=False, verbose_name='ID')),
                ('password', models.CharField(max_length=128, verbose_name='password')),
                ('last_login', models.DateTimeField(blank=True, null=True, verbose_name='last login')),
                ('is_superuser', models.BooleanField(default=False, help_text='Designates that this user has all permissions without explicitly assigning them.', verbose_name='superuser status')),
                ('fullName', models.CharField(help_text='Entre com seu nome.', max_length=255, verbose_name='Nome Completo')),
                ('email', models.EmailField(max_length=255, unique=True, verbose_name='endereço de email')),
                ('cpf', models.CharField(max_length=15, unique=True, verbose_name='cpf')),
                ('rg', models.CharField(max_length=10, unique=True, verbose_name='rg')),
                ('registration', models.CharField(max_length=9, unique=True, verbose_name='matricula')),
                ('phone1', models.CharField(max_length=13, unique=True, verbose_name='telefone 1')),
                ('phone2', models.CharField(blank=True, max_length=13, verbose_name='telefone 2')),
                ('age', models.IntegerField(verbose_name='idade')),
                ('is_staff', models.BooleanField(default=False, help_text='Designates whether the user can log into this admin site.', verbose_name='staff status')),
                ('is_active', models.BooleanField(default=True, help_text='Designates whether this user should be treated as active. Unselect this instead of deleting accounts.', verbose_name='active')),
                ('is_bse_active', models.BooleanField(default=False, verbose_name='bse_active')),
                ('date_joined', models.DateTimeField(default=django.utils.timezone.now, verbose_name='date joined')),
                ('gender', models.CharField(choices=[('M', 'Masculino'), ('F', 'Feminino')], max_length=1, verbose_name='genero')),
                ('groups', models.ManyToManyField(blank=True, help_text='The groups this user belongs to. A user will get all permissions granted to each of their groups.', related_name='user_set', related_query_name='user', to='auth.Group', verbose_name='groups')),
                ('id_course', models.ForeignKey(on_delete=django.db.models.deletion.CASCADE, to='API_REST.Course')),
                ('user_permissions', models.ManyToManyField(blank=True, help_text='Specific permissions for this user.', related_name='user_set', related_query_name='user', to='auth.Permission', verbose_name='user permissions')),
            ],
            options={
                'verbose_name': 'student',
                'verbose_name_plural': 'students',
            },
        ),
    ]