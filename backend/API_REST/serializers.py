from API_REST.models import *

from django.core.mail import send_mail

from rest_framework import serializers


class BlockSerializer(serializers.ModelSerializer):

    class Meta:
        model = Block
        fields = ('number', 'buildNumber')

class AptoSerializer(serializers.ModelSerializer):

    class Meta:
        model = Apartament
        fields = ('number', 'vacancy', 'capacity', 'id_block')

class CourseSerializer(serializers.ModelSerializer):

    class Meta:
        model = Course
        fields = ('courseName', 'duration', '_type')

class StudentSerializer(serializers.ModelSerializer):

    def __init__(self, *args, **kwargs):
        many = kwargs.pop('many', True)
        super(StudentSerializer, self).__init__(many=many, *args, **kwargs)

    class Meta:
        model = Student
        fields = ('fullName', 'registration','id_course','age','email', 'rg', 'cpf', 'phone1')

    def create(self, validated_data):
        student = Student(
            email=validated_data['email'],
            registration=validated_data['registration'],
            phone1=validated_data['phone1'],
            rg=validated_data['rg'],
            cpf=validated_data['cpf'],
            fullName=validated_data['fullName'],
            age=validated_data['age'],
            id_course=validated_data['id_course'],
        )

        senha = Student.objects.make_random_password(length=8)
        student.set_password(senha)
        student.save()

        if(Student.objects.filter(email='email') != None):
            send_mail(
                'CEU MANAGEMANT - SUA SENHA',
                'SUA SENHA É: ' + senha,
                'settings.EMAIL_HOST_USER',
                [student.email],
                fail_silently=False,
                )
        else:
            print("Não salvei")

        return student




'''class AdminSerializer(serializer_class.ModelSerializer):
    confirm_password = serializers.CharField(write_only=True)

    class Meta:
        model = Student
        fields = ('fullName', 'registration','id_course','age','email', 'rg', 'cpf', 'phone1', 'password', 'confirm_password')'''